<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;



return Application::configure(basePath: dirname(__DIR__))    
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        $middleware->alias([
            'usertimezone' => \App\Http\Middleware\SetUserTimezone::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Handle Validation errors
        $exceptions->render(function (ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return \App\Helpers\ApiResponse::error(422, 'Validation failed', ['error' => $e->errors()]);                    
            }
        });

        // Handle 404 errors
        $exceptions->render(function (NotFoundHttpException $e, $request) {
        //$exceptions->render(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) {
        //$exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return \App\Helpers\ApiResponse::error(404, 'Record not found');
                //return \App\Helpers\ApiResponse::error(404, 'Record not found', ['error' => $e->getMessage()]);
            }
        });

        // Handle generic errors
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return \App\Helpers\ApiResponse::error(500, 'Something went wrong', ['error' => $e->getMessage()]);
            }
        });
    })->create();