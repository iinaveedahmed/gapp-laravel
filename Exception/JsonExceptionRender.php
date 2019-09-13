<?php
namespace Ipaas\Gapp\Exception;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Response;
use Ipaas\Gapp\Response as GappResponse;

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
        $stack = null;
        $message = $parentMessage ?? $exception->getMessage();

        if (config('app.debug')) {
            $stack = [
                'inner' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'class' => get_class($exception),
                'line' => $exception->getLine(),
                'trace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ];
        }

        $response = new GappResponse();
        if (method_exists($exception, 'getHeaders')) {
            $response->setHeaders($exception->getHeaders() ?? []);
        }

        if (method_exists($exception, 'getStatusCode')) {
            $status = $exception->getStatusCode();
        } elseif (method_exists($exception, 'getCode')) {
            $status = $exception->getCode() > 0 ? $exception->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        } else {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
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
