<?php

namespace App\Exceptions;

use App\Domain\Helpers\ApiResponse;
use BadMethodCallException;
use ErrorException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use TypeError;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            $message = 'Page Not Found. If error persists, contact admin';
            return $this->exceptionError($exception, $message, 404);
        }

        if ($exception instanceof \InvalidArgumentException) {
            $message =  (env('APP_ENV') === 'production') ?
                'Invalid Argument' :
                'Exception: ' . $exception->getMessage();
            return $this->exceptionError($exception, $message, 400);
        }

        if ($exception instanceof TypeError) {
            $message =  (env('APP_ENV') === 'production') ?
                'Type Error, Please try again' :
                'Type Error: ' . $exception->getMessage();
            return $this->exceptionError($exception, $message, 400);
        }

        if ($exception instanceof ErrorException) {
            $message =  (env('APP_ENV') === 'production') ?
                'Error Exception, Please try again' :
                'Error Exception: ' . $exception->getMessage();
            return $this->exceptionError($exception, $message, 400);
        }
        if ($exception instanceof BadMethodCallException) {
            $message =  (env('APP_ENV') === 'production') ?
                'Error with a method call' :
                'Error with a method call: ' . $exception->getMessage();
            return $this->exceptionError($exception, $message, 500);
        }
        if ($exception instanceof UnauthorizedException) {
            return ApiResponse::responseUnauthorized();
        }

        if ($exception instanceof AuthenticationException) {
            $message =  (env('APP_ENV') === 'production') ?
                'Unauthenticated' :
                'Unauthenticated: ' . $exception->getMessage();
            return $this->exceptionError($exception, $message, 401);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $message =  (env('APP_ENV') === 'production') ?
                'Method not allowed' :
                'Exception: ' . $exception->getMessage();

            return $this->exceptionError($exception, $message, 405);
        }

        if ($exception instanceof BindingResolutionException) {
            $message =  (env('APP_ENV') === 'production') ?
                'Error' :
                'Exception: ' . $exception->getMessage();

            return $this->exceptionError($exception, $message, 405);
        }

        return parent::render($request, $exception);
    }

    /**
     * @param Throwable $exception
     * @return JsonResponse
     */
    public function exceptionError(Throwable $exception, string $message, int $statusCode = 400): JsonResponse
    {
        return ApiResponse::responseException($exception, $statusCode, $message);
    }
}
