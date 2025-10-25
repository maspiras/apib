<?php

/* use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
}); */


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Laravel\Sanctum\Sanctum;

use App\Http\Controllers\Rooms\RoomController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Reservations\ReservationController;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
 */

// Public routes
/* Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']); */

/* Route::get('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'login']);
 */
// Protected routes
Route::middleware(['auth:sanctum', 'throttle:60,1', 'usertimezone'])->group(function () {
    //Route::middleware('usertimezone')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    ######### Rooms ########
    /* Route::group(['prefix' => 'rooms'], function () {
            Route::post('/store', [RoomController::class, 'store']);
        }); */

    Route::apiResource('rooms', RoomController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('reservations', ReservationController::class);
    //});
});
//require __DIR__ . '/auth.php';

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    //    ->middleware('guest')
    ->name('login');
//Route::post('/login', [AuthController::class, 'login']);

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');
