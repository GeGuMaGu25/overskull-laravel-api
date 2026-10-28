<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Builds the standard JSON envelope used by every API response.
 *
 * Success: { "success": true,  "message": "...", "data": ... }
 * Error:   { "success": false, "message": "...", "errors": ... }
 */
class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Operación realizada correctamente',
        int $status = Response::HTTP_OK,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function created(mixed $data = null, string $message = 'Recurso creado correctamente'): JsonResponse
    {
        return self::success($data, $message, Response::HTTP_CREATED);
    }

    public static function error(
        string $message,
        int $status = Response::HTTP_BAD_REQUEST,
        mixed $errors = null,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
