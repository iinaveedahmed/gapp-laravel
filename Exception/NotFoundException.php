<?php

namespace Ipaas\Gapp\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class NotFoundException extends Exception
{
    public function __construct(
        $message = 'Not found',
        int $code = Response::HTTP_NOT_FOUND,
        Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function render()
    {
        return irenderException($this);
    }
}
