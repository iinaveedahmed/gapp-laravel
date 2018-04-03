<?php

namespace App\Ipaas;

use Carbon\Carbon;
use Closure;
use Illuminate\Validation\UnauthorizedException;

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
            // todo update service call on core
            // throw new UnauthorizedException("x-api-key mismatch");
        }

        // todo lock on app engine
        if (config('app.env') == 'production' && !$request->header('X-Appengine-Inbound-Appid')) {
            //throw new UnauthorizedException("Only accepts request from app engine");
        }

        // todo lock on cron
        if (config('app.env') == 'production' && !$request->header('X-AppEngine-Cron')) {
            //throw new UnauthorizedException("Only accepts request from app engine");
        }

        // log information
        ilog()
            ->client($request->client ?: 'Unknown')
            ->uuid($request->id ?: null)
            ->key($request->header('Authorization') ?: 'TEST')
            ->dateFrom($request->dateFrom ?: Carbon::now())
            ->dateTo($request->dateTo ?: Carbon::now());

        return $next($request);
        //UnauthorizedException();
    }
}
