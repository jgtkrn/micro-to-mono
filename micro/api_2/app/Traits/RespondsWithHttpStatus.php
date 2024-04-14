<?php

namespace App\Traits;

trait RespondsWithHttpStatus
{
    protected function success($data, $status = 200)
    {
        return response()->json(
            [
                "data" => $data,
            ],
            $status
        );
    }

    protected function failure($message, $code = 500, $errors = [])
    {
        return response()->json(
            [
                'status' => [
                    'code' => $code,
                    'message' => $message,
                    'errors' => $errors,
                ],
            ],
            $code
        );
    }
}
