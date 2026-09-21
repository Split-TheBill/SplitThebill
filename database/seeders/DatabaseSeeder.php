<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $email = config('admin.seed.email');
        $password = config('admin.seed.password');

        if (! $email || ! $password) {
            if (app()->isProduction()) {
                throw new RuntimeException(
                    'ADMIN_EMAIL and ADMIN_PASSWORD must be configured before production seeding.',
                );
            }

            $this->command?->warn('Admin seed skipped: configure ADMIN_EMAIL and ADMIN_PASSWORD.');

            return;
        }

        if (mb_strlen($password) < 16) {
            throw new RuntimeException('ADMIN_PASSWORD must contain at least 16 characters.');
        }

        User::query()->updateOrCreate([
            'email' => $email,
        ], [
            'name' => config('admin.seed.name', 'Administrator'),
            'password' => $password,
            'is_admin' => true,
        ]);
    }
}
