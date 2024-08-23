<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait JsonResponseTrait{

    protected function successResponse($status, $redirect, $message): JsonResponse
    {
        return response()->json([
            'status'    => $status,
            'redirect'    => $redirect,
            'message'       => $message
        ]);
    }

    protected function validationErrorResponse($status, $errors): JsonResponse {
        return response()->json([
            'status' => $status,
            'errors' => $errors
        ]);
    }
}