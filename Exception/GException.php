<?php

namespace Ipaas\Exception;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Google\Cloud\ErrorReporting\Bootstrap;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class GException extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        // AuthenticationException::class,
        // AuthorizationException::class,
        // HttpException::class,
        ModelNotFoundException::class,
        // TokenMismatchException::class,
        // ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        if (isset($_SERVER['GAE_SERVICE'])) {
            Bootstrap::exceptionHandler($exception);
        } else {
            parent::report($exception);
        }
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Container\EntryNotFoundException
     */
    public function render($request, Exception $exception)
    {
        $errors = null;
        $parentMessage = $exception->getMessage();
        if ($exception->getPrevious() instanceof Exception
            && strpos($exception->getFile(), 'Helper/Exception.php') >= 0
        ) {
            $exception = $exception->getPrevious();
        }

        // If the request wants JSON (AJAX doesn't always want JSON)
        if ($request->wantsJson()) {
            JsonExceptionRender::render($exception, $parentMessage);
        }

        if ($exception instanceof ValidationException) {
            foreach ($exception->validator->errors()->all() as $message) {
                $errors[] = [
                    'message' => $message,
                ];
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }
}
