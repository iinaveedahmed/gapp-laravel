<?php

namespace Ipaas;

use Exception;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Ipaas\Exception\GException;
use Ipaas\Exception\JsonExceptionRender;
use Ipaas\Logger\Client;
use Ipaas\Middleware\AuthAndLog;

class IpaasServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        require_once __DIR__ . '/../../autoload.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Add logging channel 'stackdriver'
         */
        $this->mergeConfigFrom(__DIR__ . '/Logger/config.php', 'logging.channels');

        /*
         * Init singleton ipaas-info with Ipaas/Info/Client
         */
        $this->app->singleton('ipaas-info', function () {
            return new Client();
        });

        /*
        * Init singleton ipaas-info with Ipaas/Response
        */
        $this->app->singleton('ipaas-response', function () {
            return new Response();
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
        app('Dingo\Api\Exception\Handler')->register(function (Exception $exception) {
            return JsonExceptionRender::render($exception);
        });

        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(AuthAndLog::class);
    }
}
