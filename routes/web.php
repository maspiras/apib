<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/* Route::get('/', function () {
    return view('welcome');
});


Route::post('/register/{option}', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
}); */
Route::get('/', function () {
    return response()->json([
        'message' => 'Access Denied!',
    ],403);
});
