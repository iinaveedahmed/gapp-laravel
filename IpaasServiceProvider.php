<?php

namespace Ipaas;

use Ipaas\Exception\GException;
use Ipaas\Exception\JsonExceptionRender;
use Ipaas\Exception\ValidationException;
use Dingo\Api\Exception\RateLimitExceededException;
use Google\Cloud\Core\Exception\BadRequestException;
use Google\Cloud\Core\Exception\NotFoundException;
use Illuminate\Validation\UnauthorizedException;
use Ipaas\Middleware\AuthAndLog;
use Ipaas\Logger\Client;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\CssSelector\Exception\InternalErrorException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class IpaasServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // nothing here
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Load helpers from Ipaas/Helper directory
         */
        require_once __DIR__ . '/Helper/include.php';

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
        app('Dingo\Api\Exception\Handler')->register(function (HttpException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (BadRequestException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (InternalErrorException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (NotFoundException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (RateLimitExceededException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (UnauthorizedException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (ValidationException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (ModelNotFoundException $exception) {
            return JsonExceptionRender::render($exception);
        });
        app('Dingo\Api\Exception\Handler')->register(function (QueryException $exception) {
            return JsonExceptionRender::render($exception);
        });

        $kernel = $this->app->make(Kernel::class);
        $kernel->pushMiddleware(AuthAndLog::class);
    }
}
