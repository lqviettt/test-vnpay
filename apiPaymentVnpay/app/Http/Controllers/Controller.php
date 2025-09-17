<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{    
    /**
     * sendSuccess
     *
     * @param  mixed $data
     * @param  mixed $message
     * @param  mixed $code
     * @return JsonResponse
     */
    public function sendSuccess(mixed $data, string $message = 'Successfully', int $code = 200): JsonResponse
    {
        return response()->json([
            'data' => $data,
            'status' => $code,
            'message' => $message
        ]);
    }
    
    /**
     * sendError
     *
     * @param  mixed $message
     * @param  mixed $code
     * @return JsonResponse
     */
    public function sendError(string $message, int $code = 400): JsonResponse
    {
        return response()->json([
            'status' => $code,
            'error' => $message
        ]);
    }
    
    /**
     * created
     *
     * @param  mixed $data
     * @param  mixed $message
     * @param  mixed $code
     * @return JsonResponse
     */
    public function created(mixed $data, string $message = 'Create Successfully', int $code = 201): JsonResponse
    {
        return $this->sendSuccess($data, $message, $code);
    }
}
