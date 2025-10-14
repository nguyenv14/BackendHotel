<?php

namespace App\Exceptions;

use App\Http\Resources\ApiResource;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {

            if ($exception instanceof ValidationException) {
                return ApiResource::error(
                    'Validation Failed',
                    422,
                    $exception->errors()
                )->toResponse($request);
            }

            // Authentication Exception
            if ($exception instanceof AuthenticationException) {
                return ApiResource::error(
                    'Unauthenticated',
                    401
                )->toResponse($request);
            }

            if ($exception instanceof HttpException) {
                return ApiResource::error(
                    $exception->getMessage() ?: 'HTTP Error',
                    $exception->getStatusCode()
                )->toResponse($request);
            }

            return ApiResource::error(
                $exception->getMessage() ?: 'Server Error',
                500
            )->toResponse($request);
        }

        // Nếu request không phải API thì dùng render mặc định
        return parent::render($request, $exception);
    }
}
