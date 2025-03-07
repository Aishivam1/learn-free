<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApprovedMentor
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->hasRole('mentor') || !$user->isApproved()) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be an approved mentor to perform this action.');
        }

        return $next($request);
    }
}
