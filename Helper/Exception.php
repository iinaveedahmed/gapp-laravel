<?php

if (!function_exists('iThrow')) {
    /**
     * Throw exception
     * @param Exception $exception
     * @param int $code Custom Code
     * @param null $message
     * @throws Exception
     */
    function iThrow(Exception $exception, $code, $message = null)
    {
        throw new Exception($message ?: $exception->getMessage(), $code, $exception);
    }
}

if (!function_exists('UnauthorizedException')) {
    /**
     * Throw Unauthorized Exception
     * @param Exception $exception
     * @param null $message
     * @throws Exception
     */
    function UnauthorizedException(Exception $exception, $message = null)
    {
        iThrow($exception, 401, $message);
    }
}

if (!function_exists('BadRequestException')) {
    /**
     * Throw Bad Request Exception
     * @param Exception $exception
     * @param null $message
     * @throws Exception
     */
    function BadRequestException(Exception $exception, $message = null)
    {
        iThrow($exception, 400, $message);
    }
}

if (!function_exists('TooManyRequestException')) {
    /**
     * Throw Too Many Request Exception
     * @param Exception $exception
     * @param null $message
     * @throws Exception
     */
    function TooManyRequestException(Exception $exception, $message = null)
    {
        iThrow($exception, 429, $message);
    }
}

if (!function_exists('NotFoundException')) {
    /**
     * Throw Not Found Exception
     * @param Exception $exception
     * @param null $message
     * @throws Exception
     */
    function NotFoundException(Exception $exception, $message = null)
    {
        iThrow($exception, 404, $message);
    }
}

if (!function_exists('InternalServerException')) {
    /**
     * Throw Internal Server Exception
     * @param Exception $exception
     * @param null $message
     * @throws Exception
     */
    function InternalServerException(Exception $exception, $message = null)
    {
        iThrow($exception, 500, $message);
    }
}
