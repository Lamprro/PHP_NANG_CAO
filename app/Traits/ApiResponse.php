<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(
        mixed $data = null,
        string $message = 'Thành công.',
        int $code = 200
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
            'error' => null,
        ], $code);
    }

    protected function errorResponse(
        string $message = 'Có lỗi xảy ra.',
        mixed $error = null,
        int $code = 500
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'code' => $code,
            'message' => $message,
            'data' => null,
            'error' => $error,
        ], $code);
    }
}
