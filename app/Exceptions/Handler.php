<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(\Throwable $exception)
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
    public function render($request, \Throwable $exception)
    {
        if ($exception instanceof ValidationException && $request->expectsJson()) {
            return response()->json($exception->errors(), 400);
        }

        if ($exception instanceof AuthorizationException) {
            if ($request->expectsJson()) {
                return response()->json([
                    'user' => [
                        'This action is unauthorized.'
                    ],
                ], 403);
            }
            return redirect(route('login.create'));
        }

        if ($exception instanceof ModelNotFoundException && $request->expectsJson()) {
            $model_name_segments = explode('\\', strtolower($exception->getModel()));
            $model_name = end($model_name_segments);

            return response()->json([
                $model_name => [
                    ucfirst($model_name) . ' not found.'
                ],
            ], 404);
        }

        return parent::render($request, $exception);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'user' => [
                    $exception->getMessage()
                ],
            ], 401);
        }

        return parent::unauthenticated($request, $exception);
    }
}
