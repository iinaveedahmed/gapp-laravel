<?php

namespace Ipaas\Gapp\Tests\Helpers;

use Ipaas\Gapp\Exception\BadRequestException;
use Ipaas\Gapp\Exception\InternalServerException;
use Ipaas\Gapp\Exception\NotFoundException;
use Ipaas\Gapp\Exception\TooManyRequestException;
use Ipaas\Gapp\Exception\UnauthorizedException;
use Ipaas\Gapp\Tests\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function itReturns401UnauthorizedException()
    {
        try {
            UnauthorizedException();
        } catch (UnauthorizedException $e) {
            $this->assertTrue($e instanceof UnauthorizedException);
            $this->assertEquals('Unauthorized action', $e->getMessage());
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $e->getCode());
        }

        try {
            UnauthorizedException('Custom message');
        } catch (UnauthorizedException $e) {
            $this->assertEquals('Custom message', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function itReturns400BadRequestException()
    {
        try {
            BadRequestException();
        } catch (BadRequestException $e) {
            $this->assertTrue($e instanceof BadRequestException);
            $this->assertEquals('Invalid request', $e->getMessage());
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $e->getCode());
        }

        try {
            BadRequestException('Custom message');
        } catch (BadRequestException $e) {
            $this->assertEquals('Custom message', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function itReturns429TooManyRequestException()
    {
        try {
            TooManyRequestException();
        } catch (TooManyRequestException $e) {
            $this->assertTrue($e instanceof TooManyRequestException);
            $this->assertEquals('Too many requests', $e->getMessage());
            $this->assertEquals(Response::HTTP_TOO_MANY_REQUESTS, $e->getCode());
        }

        try {
            TooManyRequestException('Custom message');
        } catch (TooManyRequestException $e) {
            $this->assertEquals('Custom message', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function itReturns404NotFoundException()
    {
        try {
            NotFoundException();
        } catch (NotFoundException $e) {
            $this->assertTrue($e instanceof NotFoundException);
            $this->assertEquals('Not found', $e->getMessage());
            $this->assertEquals(Response::HTTP_NOT_FOUND, $e->getCode());
        }

        try {
            NotFoundException('Custom message');
        } catch (NotFoundException $e) {
            $this->assertEquals('Custom message', $e->getMessage());
        }
    }

    /**
     * @test
     */
    public function itReturns500InternalServerException()
    {
        try {
            InternalServerException();
        } catch (InternalServerException $e) {
            $this->assertTrue($e instanceof InternalServerException);
            $this->assertEquals('An internal server error has occurred', $e->getMessage());
            $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $e->getCode());
        }

        try {
            InternalServerException('Custom message');
        } catch (InternalServerException $e) {
            $this->assertEquals('Custom message', $e->getMessage());
        }
    }
}
