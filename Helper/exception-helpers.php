<?php

if (!function_exists('UnauthorizedException')) {
    /**
     * @param null $message
     * @throws \Ipaas\Exception\UnauthorizedException
     */
    function UnauthorizedException($message = null)
    {
        throw new \Ipaas\Exception\UnauthorizedException($message);
    }
}

if (!function_exists('BadRequestException')) {
    /**
     * @param null $message
     * @throws \Ipaas\Exception\BadRequestException
     */
    function BadRequestException($message = null)
    {
        throw new \Ipaas\Exception\BadRequestException($message);
    }
}

if (!function_exists('TooManyRequestException')) {
    /**
     * @param null $message
     * @throws \Ipaas\Exception\TooManyRequestException
     */
    function TooManyRequestException($message = null)
    {
        throw new \Ipaas\Exception\TooManyRequestException($message);
    }
}

if (!function_exists('NotFoundException')) {
    /**
     * @param null $message
     * @throws \Ipaas\Exception\NotFoundException
     */
    function NotFoundException($message = null)
    {
        throw new \Ipaas\Exception\NotFoundException($message);
    }
}

if (!function_exists('InternalServerException')) {
    /**
     * @param null $message
     * @throws \Ipaas\Exception\InternalServerException
     */
    function InternalServerException($message = null)
    {
        throw new \Ipaas\Exception\InternalServerException($message);
    }
}
