<?php
namespace App\Traits;

trait ResponseWithError {
    public function responseWithError($code, $message, $errors = []) {
        return response()->json([
            'status' => [
                'code' => $code,
                'message' => $message,
                'errors' => $errors,
            ],
        ], $code);
    }
}
