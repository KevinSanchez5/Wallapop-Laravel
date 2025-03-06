<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserRoleAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'cliente') {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
