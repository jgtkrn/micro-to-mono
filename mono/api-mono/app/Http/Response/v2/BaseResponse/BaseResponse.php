<?php

namespace App\Http\Response\v2\BaseResponse;

class BaseResponse
{
    public function generate($data, int $code, string $message, bool $status)
    {
        return response()->json(
            [
                'code' => $code,
                'status' => $status ?? true,
                'data' => $data,
                'message' => $message,
            ],
            $code);
    }
}
