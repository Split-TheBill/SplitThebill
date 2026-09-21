<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function public(string $path): Response
    {
        return $this->mediaResponse(
            'database-public',
            $path,
            'public, max-age=86400',
        );
    }

    public function private(string $path): Response
    {
        return $this->mediaResponse(
            'database-private',
            $path,
            'no-store, no-cache, must-revalidate, max-age=0',
        );
    }

    private function mediaResponse(string $diskName, string $path, string $cacheControl): Response
    {
        $disk = Storage::disk($diskName);

        abort_unless($disk->exists($path), 404);

        $contents = $disk->get($path);
        $etag = '"'.hash('sha256', $contents).'"';

        if (request()->header('If-None-Match') === $etag) {
            return response('', 304, [
                'Cache-Control' => $cacheControl,
                'ETag' => $etag,
            ]);
        }

        return response($contents, 200, [
            'Cache-Control' => $cacheControl,
            'Content-Length' => (string) strlen($contents),
            'Content-Type' => $disk->mimeType($path) ?: 'application/octet-stream',
            'ETag' => $etag,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
