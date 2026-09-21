<?php

namespace App\Filesystem;

use DateTimeInterface;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\URL;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;

class DatabaseFilesystemAdapter implements FilesystemAdapter
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly string $bucket,
        private readonly string $visibility,
        private readonly ?string $publicRoute = null,
        private readonly ?string $temporaryRoute = null,
    ) {}

    public function fileExists(string $path): bool
    {
        return $this->query()->where('path', $path)->exists();
    }

    public function directoryExists(string $path): bool
    {
        $prefix = $this->directoryPrefix($path);

        return $this->query()
            ->when($prefix !== '', fn ($query) => $query->where('path', 'like', $this->escapeLike($prefix).'%'))
            ->exists();
    }

    public function write(string $path, string $contents, Config $config): void
    {
        try {
            $now = now();

            $this->query()->updateOrInsert(
                ['bucket' => $this->bucket, 'path' => $path],
                fn (bool $exists): array => [
                    'contents' => base64_encode($contents),
                    'size' => strlen($contents),
                    'mime_type' => $this->detectMimeType($contents, $config),
                    'visibility' => $config->get('visibility', $this->visibility),
                    'last_modified' => $now->getTimestamp(),
                    'updated_at' => $now,
                    ...$exists ? [] : ['created_at' => $now],
                ],
            );
        } catch (\Throwable $exception) {
            throw UnableToWriteFile::atLocation($path, $exception->getMessage(), $exception);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $data = stream_get_contents($contents);

        if ($data === false) {
            throw UnableToWriteFile::atLocation($path, 'The uploaded stream could not be read.');
        }

        $this->write($path, $data, $config);
    }

    public function read(string $path): string
    {
        $file = $this->find($path);
        $contents = base64_decode($file->contents, true);

        if ($contents === false) {
            throw UnableToReadFile::fromLocation($path, 'The stored contents are invalid.');
        }

        return $contents;
    }

    public function readStream(string $path)
    {
        $stream = fopen('php://temp', 'w+b');

        if ($stream === false || fwrite($stream, $this->read($path)) === false) {
            throw UnableToReadFile::fromLocation($path, 'A readable stream could not be created.');
        }

        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $this->query()->where('path', $path)->delete();
    }

    public function deleteDirectory(string $path): void
    {
        $prefix = $this->directoryPrefix($path);

        $this->query()
            ->when($prefix !== '', fn ($query) => $query->where('path', 'like', $this->escapeLike($prefix).'%'))
            ->delete();
    }

    public function createDirectory(string $path, Config $config): void
    {
        // Directories are implicit: they exist as soon as a file uses their prefix.
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $updated = $this->query()->where('path', $path)->update([
            'visibility' => $visibility,
            'updated_at' => now(),
        ]);

        if ($updated === 0) {
            throw UnableToSetVisibility::atLocation($path, 'The file does not exist.');
        }
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->attributes($path);
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->attributes($path);
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->attributes($path);
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->attributes($path);
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $prefix = $this->directoryPrefix($path);
        $directories = [];

        $files = $this->query()
            ->when($prefix !== '', fn ($query) => $query->where('path', 'like', $this->escapeLike($prefix).'%'))
            ->orderBy('path')
            ->get(['path', 'size', 'visibility', 'last_modified', 'mime_type']);

        foreach ($files as $file) {
            $relativePath = substr($file->path, strlen($prefix));

            if (! $deep && str_contains($relativePath, '/')) {
                $directory = $prefix.strstr($relativePath, '/', true);

                if (! isset($directories[$directory])) {
                    $directories[$directory] = true;
                    yield new DirectoryAttributes($directory, $this->visibility);
                }

                continue;
            }

            yield $this->attributesFromRecord($file);
        }
    }

    public function move(string $source, string $destination, Config $config): void
    {
        if ($source === $destination) {
            throw UnableToMoveFile::sourceAndDestinationAreTheSame($source, $destination);
        }

        try {
            $this->connection->transaction(function () use ($source, $destination): void {
                $this->find($source);

                $this->query()->where('path', $destination)->delete();
                $this->query()->where('path', $source)->update([
                    'path' => $destination,
                    'updated_at' => now(),
                ]);
            });
        } catch (\Throwable $exception) {
            throw UnableToMoveFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        if ($source === $destination) {
            throw UnableToCopyFile::sourceAndDestinationAreTheSame($source, $destination);
        }

        try {
            $file = $this->find($source);
            $now = now();

            $this->query()->updateOrInsert(
                ['bucket' => $this->bucket, 'path' => $destination],
                fn (bool $exists): array => [
                    'contents' => $file->contents,
                    'size' => $file->size,
                    'mime_type' => $file->mime_type,
                    'visibility' => $file->visibility,
                    'last_modified' => $now->getTimestamp(),
                    'updated_at' => $now,
                    ...$exists ? [] : ['created_at' => $now],
                ],
            );
        } catch (\Throwable $exception) {
            throw UnableToCopyFile::fromLocationTo($source, $destination, $exception);
        }
    }

    public function getUrl(string $path): string
    {
        if ($this->publicRoute === null || $this->visibility !== Visibility::PUBLIC) {
            throw new \RuntimeException('This database disk does not expose public URLs.');
        }

        return URL::route($this->publicRoute, ['path' => $path]);
    }

    public function getTemporaryUrl(string $path, DateTimeInterface $expiration, array $options = []): string
    {
        if ($this->temporaryRoute === null) {
            throw new \RuntimeException('This database disk does not expose temporary URLs.');
        }

        return URL::temporarySignedRoute($this->temporaryRoute, $expiration, ['path' => $path]);
    }

    private function query()
    {
        return $this->connection->table('stored_media')->where('bucket', $this->bucket);
    }

    private function find(string $path): object
    {
        $file = $this->query()->where('path', $path)->first();

        if (! $file) {
            throw UnableToReadFile::fromLocation($path, 'The file does not exist.');
        }

        return $file;
    }

    private function attributes(string $path): FileAttributes
    {
        try {
            return $this->attributesFromRecord($this->find($path));
        } catch (UnableToReadFile $exception) {
            throw UnableToRetrieveMetadata::create($path, 'metadata', $exception->getMessage(), $exception);
        }
    }

    private function attributesFromRecord(object $file): FileAttributes
    {
        return new FileAttributes(
            $file->path,
            (int) $file->size,
            $file->visibility,
            (int) $file->last_modified,
            $file->mime_type,
        );
    }

    private function detectMimeType(string $contents, Config $config): string
    {
        $configured = $config->get('mimetype');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        return $finfo->buffer($contents) ?: 'application/octet-stream';
    }

    private function directoryPrefix(string $path): string
    {
        return $path === '' ? '' : rtrim($path, '/').'/';
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
