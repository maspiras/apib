<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if(isset(auth()->user()->host->host_settings->timezone) && !empty(auth()->user()->host->host_settings->timezone)    ){
                date_default_timezone_set(auth()->user()->host->host_settings->timezone);
            }
        }
        return $next($request);
    }
}
