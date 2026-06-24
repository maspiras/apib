<?php
use App\Modules\Property\Http\V1\Controllers\PropertyController;

Route::prefix('properties')
    ->middleware(['throttle:60,1', 'usertimezone'])
    ->group(function () {
        Route::get('/', [  PropertyController::class, 'index' ]);
        Route::get('/{property}', [ PropertyController::class, 'show' ]);
        Route::get('/{property}/rooms', [ PropertyController::class, 'rooms']);        
});