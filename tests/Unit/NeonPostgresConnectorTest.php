<?php

namespace Tests\Unit;

use App\Database\NeonPostgresConnector;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NeonPostgresConnectorTest extends TestCase
{
    public function test_it_adds_the_neon_endpoint_to_the_postgres_dsn(): void
    {
        $connector = new class extends NeonPostgresConnector
        {
            public function dsn(array $config): string
            {
                return $this->getDsn($config);
            }
        };

        $dsn = $connector->dsn([
            'host' => 'ep-example-123.neon.tech',
            'database' => 'neondb',
            'port' => 5432,
            'neon_endpoint' => 'ep-example-123',
        ]);

        $this->assertStringContainsString("options='endpoint=ep-example-123'", $dsn);
    }

    public function test_it_rejects_an_invalid_neon_endpoint(): void
    {
        $connector = new class extends NeonPostgresConnector
        {
            public function dsn(array $config): string
            {
                return $this->getDsn($config);
            }
        };

        $this->expectException(InvalidArgumentException::class);

        $connector->dsn([
            'database' => 'neondb',
            'neon_endpoint' => "ep-example';sslmode=disable",
        ]);
    }
}
