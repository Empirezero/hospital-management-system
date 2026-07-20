<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->force_password_change) {
            // Allow the change-password page itself and logout, or you'd get a redirect loop
            if (!$request->routeIs('password.force.edit', 'password.force.update', 'logout')) {
                return redirect()->route('password.force.edit');
            }
        }

        return $next($request);
    }
}
