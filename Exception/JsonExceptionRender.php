<?php
namespace Ipaas\Exception;

use Exception;

class JsonExceptionRender
{
    public static function render(Exception $exception, $parentMessage = null)
    {
        $errors = null;

        if ($exception instanceof ValidationException) {
            foreach ($exception->validator->errors() as $message) {
                $errors[] = [
                    'message' => $message,
                ];
            }
        }

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
        } else {
            $status = 500;
        }

        return $response->sendError($message, $status, md5(uniqid()), $errors, $stack);
    }
}
