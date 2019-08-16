<?php

namespace Ipaas\Gapp;

use Exception;
use Illuminate\Support\ServiceProvider;
use Ipaas\Gapp\Command\CreatePartnerApp;
use Ipaas\Gapp\Exception\GException;
use Ipaas\Gapp\Exception\JsonExceptionRender;
use Ipaas\Gapp\Logger\Client;
use Ipaas\Gapp\Middleware\AuthAndLog;

class IpaasServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migration');

        $this->commands([
            CreatePartnerApp::class,
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Add logging channel 'stack-driver'
         */
        $this->mergeConfigFrom(__DIR__ . '/Logger/config.php', 'logging.channels');

        /*
         * Init singleton ipaas-info with Ipaas/Info/Client
         */
        $this->app->singleton('logger-context', function () {
            return new Client();
        });

        /*
        * Take over exception handler
        */
        $this->app->singleton(
            'Illuminate\Contracts\Debug\ExceptionHandler',
            GException::class
        );

        /**
         * Register dingo handlers
         */
        $this->app->bind(
            'Dingo\Api\Exception\Handler',
            function (Exception $exception) {
                return JsonExceptionRender::render($exception);
            });

        app('router')->aliasMiddleware('partner', AuthAndLog::class);
    }
}
