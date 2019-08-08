<?php

namespace Ipaas\Gapp\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class UnauthorizedException extends Exception
{
    public function __construct(
        $message = 'Unauthorized action',
        int $code = Response::HTTP_UNAUTHORIZED,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return irenderException($this);
    }
}
