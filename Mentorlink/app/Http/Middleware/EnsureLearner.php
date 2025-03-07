<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureLearner
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->hasRole('learner')) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Learner privileges required.');
        }

        return $next($request);
    }
}
