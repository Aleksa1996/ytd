<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\GeneralException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // model not found
        if ($exception instanceof ModelNotFoundException) {
            return response()->json(['error_type' => 'general_errors', 'message' => '404 Not Found.'], 404);
        }

        // request validation
        if ($exception instanceof ValidationException) {
            $errorCollection = collect($exception->errors());
            $errorCollection = $errorCollection->map(function ($error, $key) {
                return ['field' => $key, 'message' => $error[0]];
            });
            return response()->json(['error_type' => 'form_errors', 'errors' => $errorCollection->values()], 422);
        }

        // general error messages (custom exception)
        if ($exception instanceof GeneralException) {
            return response()->json(['error_type' => 'general_errors', 'message' => $exception->getMessage()], 422);
        }

        // handling throttle request exception message
        if ($exception instanceof ThrottleRequestsException) {
            return response()->json(['error_type' => 'general_errors', 'message' => 'Too many attempts.'], 429);
        }

        // default JSON response in production
        if (env('APP_ENV') !== 'local') {
            return response()->json(['error_type' => 'general_errors', 'message' => $exception->getMessage()], 500);
        }

        return parent::render($request, $exception);
    }
}
