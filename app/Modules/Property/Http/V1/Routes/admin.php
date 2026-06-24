<?php


Route::prefix('admin')
    ->middleware([
        'auth:sanctum',
        'admin'
    ])
    ->group(function () {

        Route::get('/properties', [
            AdminPropertyController::class,
            'index'
        ]);

        Route::delete('/properties/{property}', [
            AdminPropertyController::class,
            'destroy'
        ]);
    });