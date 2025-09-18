<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = [], $meta = [])
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'meta' => array_merge($meta, [
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid()
            ]),
            'error' => null
        ]);
    }

    public static function error($code = 400, $message = 'Something went wrong', $details = [])
    {
        /* 
        Error Codes 
        200 standard success
        201 created
        204 no content
        400 bad request
        401 unauthorized
        403 forbidden
        404 not found
        422 unprocessable entity (validation error)
        429 Too many requests
        500 internal server error
        501 not implemented
        502 bad gateway
        503 service unavailable
        504 gateway timeout
        511 network authentication required
        */
        return response()->json([
            'status' => 'error',
            'data' => null,
            'meta' => [
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid()
            ],
            'error' => [
                'code' => $code,
                'message' => $message,
                'details' => $details
            ]
        ], $code);
    }

    public static function paginated($paginator, $meta = [])
    {
        return response()->json([
            'status' => 'success',
            'data' => $paginator->items(),
            'meta' => array_merge($meta, [
                'timestamp' => now()->toISOString(),
                'request_id' => uniqid(),
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'has_next' => $paginator->hasMorePages(),
                'has_prev' => $paginator->currentPage() > 1
            ]),
            'error' => null
        ]);
    }
}