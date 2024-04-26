<?php

namespace App\Providers;

use App\Services\CatsAPI\CatsClient;
use App\Services\CatsAPI\CatsClientInterface;
use App\Services\CatsAPI\CatsService;
use App\Services\CatsAPI\CatsServiceInterface;
use App\Services\CatsAPI\FakeCatsService;
use App\Services\CatsAPI\NullCatsClient;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class CatsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CatsClientInterface::class, function (Application $app) {
            if ($app->isLocal() || $app->isProduction()) {
                return new CatsClient();
            }

            return new NullCatsClient();
        });

        $this->app->bind(CatsServiceInterface::class, function (Application $app) {

            if ($app->isLocal() || $app->isProduction()) {
                return app(CatsService::class);
            }

            return new FakeCatsService();
        });
    }

    public function boot(): void
    {
        //
    }
}
