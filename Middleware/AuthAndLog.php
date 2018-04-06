<?php

namespace Ipaas\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\UnauthorizedException;

/**
 * Middleware AuthAndLog
 * This will provide ability to verify IPAAS header params and initiate basic log context.
 * header check: x-api-key, X-Appengine, X-AppEngine-Cron. Any un-matched request throw 401 UnauthorizedException.
 * log context: type, request->client, request->uuid, request->dateFrom, request->dateTo and auth header for client key.
 * @package Ipaas
 */
class AuthAndLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        // auth api key
        if (!$request->has('x-api-key') && $request['x-api-key'] != env('API_KEY', 'development')) {
            Log::alert('Unauthorized request');
            // todo update service call on core
            // throw new UnauthorizedException("x-api-key mismatch");
        }

        // todo lock on app engine
        if (config('app.env') == 'production' && !$request->header('X-Appengine-Inbound-Appid')) {
            ilog()->type('default');
            //throw new UnauthorizedException("Only accepts request from app engine");
        }

        // todo lock on cron
        if (config('app.env') == 'production' && !$request->header('X-AppEngine-Cron')) {
            ilog()->type('corn');
            //throw new UnauthorizedException("Only accepts request from app engine");
        }

        // log information
        /** @noinspection PhpUndefinedFieldInspection */
        ilog()
            ->client($request->client ?: 'Unknown')
            ->uuid($request->uuid ?: null)
            ->key($request->header('Authorization') ?: 'TEST')
            ->dateFrom($request->dateFrom ?: Carbon::now())
            ->dateTo($request->dateTo ?: Carbon::now());

        return $next($request);
    }
}
