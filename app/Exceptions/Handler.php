<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        AuthenticationException::class,
        ValidationException::class,
        \App\Exceptions\AIServiceUnavailableException::class,
        \App\Exceptions\InvalidAIResponseException::class,
    ];

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        $this->renderable(function (NotFoundHttpException $e) {
            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Resource not found'
                ], 404);
            }
        });

        $this->renderable(function (AuthenticationException $e) {
            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }
        });

        $this->renderable(function (ValidationException $e) {
            if (request()->is('api/*')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $this->renderable(function (\App\Exceptions\AIServiceUnavailableException $e) {
            return response()->json([
                'message' => 'AI Service is temporarily unavailable',
                'error' => 'service_unavailable'
            ], 503);
        });

        $this->renderable(function (\App\Exceptions\InvalidAIResponseException $e) {
            return response()->json([
                'message' => 'Invalid response from AI service',
                'error' => 'invalid_response'
            ], 422);
        });
    }
} 