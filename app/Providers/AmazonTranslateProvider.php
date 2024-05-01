<?php

namespace App\Providers;

use App\Services\TranslateAPI\TranslateService;
use App\Services\TranslateAPI\TranslateServiceInterface;
use Aws\Comprehend\ComprehendClient;
use Aws\Translate\TranslateClient;
use Illuminate\Support\ServiceProvider;

class AmazonTranslateProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(TranslateClient::class, function () {
            return new TranslateClient(
                [
                    'credentials' => [
                        'key' => (string) config('services.translate.key'),
                        'secret' => (string) config('services.translate.secret')
                    ],
                    'region' => (string) config('services.translate.region'),
                    'version' => 'latest'
                ]
            );
        });

        $this->app->bind(ComprehendClient::class, function () {
            return new ComprehendClient(
                [
                    'credentials' => [
                        'key' => (string) config('services.translate.key'),
                        'secret' => (string) config('services.translate.secret')
                    ],
                    'region' => (string) config('services.translate.region'),
                    'version' => 'latest'
                ]
            );
        });

        $this->app->bind(TranslateServiceInterface::class, function () {
            return app(TranslateService::class);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
