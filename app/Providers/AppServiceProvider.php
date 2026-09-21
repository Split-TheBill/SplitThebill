<?php

namespace App\Providers;

use App\Database\NeonPostgresConnector;
use App\Filesystem\DatabaseFilesystemAdapter;
use App\Models\ProductSubscription;
use App\Observers\ProductSubscriptionObserver;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('db.connector.pgsql', fn () => new NeonPostgresConnector);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Storage::extend('database', function ($app, array $config): FilesystemAdapter {
            $adapter = new DatabaseFilesystemAdapter(
                DB::connection($config['connection'] ?? null),
                $config['bucket'],
                $config['visibility'],
                $config['public_route'] ?? null,
                $config['temporary_route'] ?? null,
            );

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config,
            );
        });

        ProductSubscription::observe(ProductSubscriptionObserver::class);
    }
}
