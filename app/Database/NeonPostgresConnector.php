<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector;
use InvalidArgumentException;

class NeonPostgresConnector extends PostgresConnector
{
    /**
     * Build a PostgreSQL DSN that supports Neon on clients without TLS SNI.
     */
    protected function getDsn(array $config): string
    {
        $dsn = parent::getDsn($config);
        $endpoint = $config['neon_endpoint'] ?? null;

        if (! is_string($endpoint) || $endpoint === '') {
            return $dsn;
        }

        if (! preg_match('/\Aep-[a-z0-9-]+\z/i', $endpoint)) {
            throw new InvalidArgumentException('Invalid Neon endpoint identifier.');
        }

        return "{$dsn};options='endpoint={$endpoint}'";
    }
}
