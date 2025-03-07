<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureMentor
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasRole('mentor')) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Mentor privileges required.');
        }

        return $next($request);
    }
}
