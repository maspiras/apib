<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)//: Response
    {
        $request->authenticate();

        $user = Auth::user();

        //$request->session()->regenerate();

        //return response()->noContent();
        $token = $user->createToken('mobile-token')->plainTextToken;

        /* return response()->json([
            'user' => $user,
            'token' => $token
        ], 200); */
        $data = $user->toArray();
        $data['host_id'] = $user->host->id;
        $data['token'] = $token;
        return ApiResponse::success($data, ['message' => 'You are logged in']);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): Response
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->noContent();
    }
}
