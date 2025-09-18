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