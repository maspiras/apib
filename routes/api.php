<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Sanctum\Sanctum;

use App\Http\Controllers\Rooms\RoomController;
/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 */

// Public routes
Route::post('/register/{option}', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/* Route::get('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'login']);
 */
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    ######### Rooms ########
    Route::group(['prefix' => 'rooms'], function () {
        Route::post('/store', [RoomController::class, 'store']);        
    });
    
});

