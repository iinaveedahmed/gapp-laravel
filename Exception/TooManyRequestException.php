<?php

namespace Ipaas\Gapp\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TooManyRequestException extends Exception
{
    public function __construct(
        $message = 'Too many requests',
        int $code = Response::HTTP_TOO_MANY_REQUESTS,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return irenderException($this);
    }
}
