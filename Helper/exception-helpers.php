<?php

use Ipaas\Gapp\Exception\BadRequestException;
use Ipaas\Gapp\Exception\InternalServerException;
use Ipaas\Gapp\Exception\NotFoundException;
use Ipaas\Gapp\Exception\TooManyRequestException;
use Ipaas\Gapp\Exception\UnauthorizedException;

if (!function_exists('UnauthorizedException')) {
    /**
     * @param null $message
     * @throws UnauthorizedException
     */
    function UnauthorizedException($message = null)
    {
        if ($message) {
            throw new UnauthorizedException($message);
        }

        throw new UnauthorizedException;
    }
}

if (!function_exists('BadRequestException')) {
    /**
     * @param null $message
     * @throws BadRequestException
     */
    function BadRequestException($message = null)
    {
        if ($message) {
            throw new BadRequestException($message);
        }

        throw new BadRequestException;
    }
}

if (!function_exists('TooManyRequestException')) {
    /**
     * @param null $message
     * @throws TooManyRequestException
     */
    function TooManyRequestException($message = null)
    {
        if ($message) {
            throw new TooManyRequestException($message);
        }

        throw new TooManyRequestException;
    }
}

if (!function_exists('NotFoundException')) {
    /**
     * @param null $message
     * @throws NotFoundException
     */
    function NotFoundException($message = null)
    {
        if ($message) {
            throw new NotFoundException($message);
        }

        throw new NotFoundException;
    }
}

if (!function_exists('InternalServerException')) {
    /**
     * @param null $message
     * @throws InternalServerException
     */
    function InternalServerException($message = null)
    {
        if ($message) {
            throw new InternalServerException($message);
        }

        throw new InternalServerException;
    }
}
