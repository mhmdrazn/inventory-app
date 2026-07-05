<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * Uniform envelope for API responses.
 *
 * Every JSON response returned from Api\V1 controllers (or from the global
 * exception handler for /api/* requests) uses:
 *
 *   { "success": bool, "message": string, "data": mixed }
 *
 * so consumers only ever have to branch on `success` and read `message` / `data`.
 */
trait ApiResponse
{
    /**
     * Return a successful response envelope.
     */
    protected function success(mixed $data = null, string $message = 'OK', int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * Return an error response envelope.
     *
     * @param  mixed  $data  Additional context (validation errors, etc.). Omit for a bare error.
     */
    protected function error(string $message, int $status = Response::HTTP_BAD_REQUEST, mixed $data = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
