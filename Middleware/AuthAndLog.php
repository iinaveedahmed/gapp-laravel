<?php

namespace Ipaas\Gapp\Middleware;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Ipaas\Gapp\Model\PartnerApp;

/**
 * Middleware AuthAndLog
 * This will provide ability to verify IPAAS header params and initiate basic log context.
 * header check: X-Api-Key. Any un-matched request throw 401 UnauthorizedException.
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

        if (config('gapp.secure') && $this->isInvalidApiKey()) {
            ilog()->setType('default');
            Log::alert('Unauthorized request');
            abort(Response::HTTP_UNAUTHORIZED, 'Only accepts request from app engine with a valid X-Api-Key');
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
            /** @noinspection PhpUndefinedMethodInspection */
            $apiKeyExists = PartnerApp::where('api_key', $apiKey)->where('is_active', true)->exists();
            return !$apiKeyExists;
        } catch (ModelNotFoundException $e) {
            report($e);
            $errorMessage = 'The `partner_apps` table is not created, ' .
                'try running the `php artisan migrate` command';
            abort(Response::HTTP_UNAUTHORIZED, $errorMessage);
        }

        return true;
    }

    /**
     * @param Request $request
     * @throws Exception
     */
    private function setILogFields(Request $request): void
    {
        ilog()
            ->setClientId($request->header('Authorization'))
            ->setClientKey($request->header('X-Api-Key'))
            ->setRequestId($request->header('Gapp-Request-ID'))
            ->setUuid($request->get('uuid'))
            ->setDateFrom($request->get('dateFrom'))
            ->setDateTo($request->get('dateTo'));
    }
}
