<?php
use App\Modules\Property\Http\V1\Controllers\MyPropertyController;


Route::prefix('me')
    ->middleware(['auth:sanctum', 'throttle:60,1', 'usertimezone'])
    ->group(function () {
        Route::get('/properties', [ MyPropertyController::class, 'index'  ]);
        Route::post('/properties', [  MyPropertyController::class, 'store' ]);
        Route::put('/properties/{property}', [ MyPropertyController::class, 'update' ]);
        Route::post('/properties/{property}/publish', [ MyPropertyController::class, 'publish' ]);
        Route::post('/properties/{property}/archive', [ MyPropertyController::class, 'archive' ]);
    });