<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictInvestigator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, $next)
    {
        \Log::info('RestrictInvestigator: user role = ' . auth()->user()->role);
        
        if (auth()->user()->role == 'investigator') {
            abort(403, 'Investigators are not allowed here.');
        }
        return $next($request);
    }
}
