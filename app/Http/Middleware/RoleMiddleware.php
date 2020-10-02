<?php

namespace App\Http\Middleware;

use Closure;

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (auth()->guest()) {
            abort(403);
        }

        $roles = is_array($role) ? $role : explode('|', $role);

        if (!auth()->user()->hasRole($roles)) {
            abort(403);
        }

        return $next($request);
    }
}
