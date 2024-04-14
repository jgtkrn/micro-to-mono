<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
            if (app()->bound('sentry')) {
              app('sentry')->captureException($e);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->wantsJson() && $request->is('assessments-api/*')) {
                $validationErrors = $e->validator->errors()->messages();
                $errors = [];
                foreach ($validationErrors as $field => $messages) {
                    $errors[] = [
                        'field' => $field,
                        'message' => $messages[0],
                    ];
                }

                return response()->json([
                    'status' => [
                        'code' => 422,
                        'message' => '',
                        'errors' => $errors,
                    ],
                ], 422);
            }
        });
    }

    /**
     * render response to json instead of 
     * webpage error if model not found 404 
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ModelNotFoundException && $request->wantsJson()) {
            return response()->json(['message' => $exception->getMessage()], 404);
        }

        return parent::render($request, $exception);
    }
}
