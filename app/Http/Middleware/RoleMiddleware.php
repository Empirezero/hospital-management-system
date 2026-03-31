<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        // Check if the user is authenticated and has the specified role
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'Unauthorized action.'); // Return a 403 Forbidden response
        }

        return $next($request);
    }
}

