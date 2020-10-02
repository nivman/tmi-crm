<?php

namespace App\Http\Middleware;

use Closure;

class RedirectIfNotAjaxRequest
{
    public function handle($request, Closure $next)
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        if (!request()->ajax() && request()->is('app/*')) {
            return redirect('/');
        }

        return $next($request);
    }
}
