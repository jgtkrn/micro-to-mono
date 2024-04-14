<?php

namespace App\Traits;

trait RespondsWithHttpStatus
{
    protected function success($data = [], $status = 200)
    {
        return response()->json(
            [
                'data' => $data,
            ],
            $status
        );
    }

    protected function successWithMeta($data = [], $meta = [], $status = 200)
    {
        return response()->json(
            [
                'data' => $data,
                'meta' => $meta,
            ],
            $status
        );
    }

    protected function failure($message, $status = 500)
    {
        return response()->json(
            [
                'status' => [
                    'code' => $status,
                    'message' => $message,
                    'errors' => [],
                ],
            ],
            $status
        );
    }
}
