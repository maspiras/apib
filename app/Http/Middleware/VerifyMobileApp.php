<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMobileApp
{
      /**
       * Handle an incoming request.
       *
       * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
       */
      public function handle(Request $request, Closure $next): Response
      {
            if ($request->header('X-API-KEY') !== config('app.app_key_mobile')) {
                  return response()->json(['message' => 'Invalid mobile application key'], 403);
            }
            return $next($request);
      }
}
