<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, $next)
    {
        \Log::info('AdminAuthMiddleware: user? ' . (auth()->check() ? 'Yes' : 'No'));
        if (auth()->check()) {
            \Log::info('AdminAuthMiddleware: role = ' . auth()->user()->role);
        }
    
        // YOUR EXISTING LOGIC (probably checking if user is authenticated)
        if (!auth()->check()) {
            abort(403, 'Unauthenticated');
        }
    
        return $next($request);
}
}
