<?php

namespace Ipaas\Gapp\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * @param Request $request
     * @param Closure $next
     * @return mixed
     * @throws Exception
     */
    public function handle(Request $request, Closure $next)
    {
        $this->setILogFields($request);

        if ($this->isInvalidApiKey()) {
            ilog()->setType('default');
            Log::alert('Unauthorized request');
            abort(Response::HTTP_UNAUTHORIZED, 'Only accepts request from app engine with a valid x-api-key');
        }

        return $next($request);
    }

    /**
     * @return bool
     */
    private function isInvalidApiKey(): bool
    {
        $apiKey = ilog()->getClientKey();
        if ($apiKey == 'Unknown') {
            return true;
        }

        try {
            $apiKeyExists = DB::table('auths')->whereApiKey($apiKey)->exists();
        } catch (Exception $e) {
            abort(Response::HTTP_UNAUTHORIZED, 'The `auths` table is not created');
        }

        return !$apiKeyExists;
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function setILogFields(Request $request): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        ilog()
            ->setClientId($request->header('Authorization'))
            ->setClientKey($request->header('x-api-key'))
            ->setRequestId($request->header('Amaka-Request-ID'))
            ->setUuid($request->uuid)
            ->setDateFrom($request->dateFrom)
            ->setDateTo($request->dateTo);
    }
}
