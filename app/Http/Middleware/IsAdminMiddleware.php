<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->user()?->is_admin) {
            return to_route('index');
        }
        return $next($request);
    }
}
