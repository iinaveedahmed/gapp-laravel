<?php
namespace Ipaas\Exception;

use Exception;
use Illuminate\Validation\ValidationException;

class JsonExceptionRender
{
    /**
     * @param Exception $exception
     * @param null $parentMessage
     * @return mixed
     */
    public static function render(Exception $exception, $parentMessage = null)
    {
        $errors = null;

        $message = $parentMessage ?: $exception->getMessage();
        $stack = null;

        if (config('app.debug')) {
            $stack = [
                'inner' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'class' => get_class($exception),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString()),
            ];
        }

        $response = iresponse();
        if (method_exists($exception, 'getHeaders')) {
            $response->setHeaders($exception->getHeaders() ?? []);
        }

        if (method_exists($exception, 'getStatusCode')) {
            $status = $exception->getStatusCode();
        } elseif (method_exists($exception, 'getCode')) {
            $status = $exception->getCode() > 0 ? $exception->getCode() : 500;
        } else {
            $status = 500;
        }

        if ($exception instanceof ValidationException) {
            /** @var ValidationException $exception */
            $errors = $exception->errors();
            $status = $exception->status;
            $message = $exception->response;
        }

        return $response->sendError($message, $status, md5(uniqid()), $errors, $stack);
    }
}
