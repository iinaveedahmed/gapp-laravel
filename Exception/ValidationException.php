<?php
namespace Ipaas\Exception;

use Exception;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationException extends HttpException
{
    /**
     * @var Validator
     */
    public $validator;

    /**
     * ValidationException constructor.
     * @param $validator
     * @param null $message
     * @param null $code
     * @param Exception|null $previous
     */
    public function __construct(Validator $validator, $message = null, $code = null, Exception $previous = null)
    {
        parent::__construct(422, $message, $previous, [], 422);
        $this->validator = $validator;
    }
}
