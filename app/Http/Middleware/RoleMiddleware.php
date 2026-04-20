<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Flatten comma-separated roles e.g. 'admin,doctor,nurse'
        $allowed = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $allowed[] = trim($r);
            }
        }

        if (!in_array(Auth::user()->role, $allowed)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
