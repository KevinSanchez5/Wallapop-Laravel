<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRoleAuth
{

    /**
     * Maneja la solicitud HTTP e impone la restricción del rol de administrador.
     *
     * Este método verifica si el usuario autenticado tiene el rol de "admin". Si es así,
     * permite que la solicitud continúe. Si no, aborta la solicitud y devuelve un error 403.
     *
     * @param Request $request La solicitud HTTP entrante.
     * @param Closure $next La siguiente acción o middleware que se ejecutará si la validación es exitosa.
     *
     * @return Response Permite continuar con la solicitud si el usuario tiene el rol de admin,
     * o aborta la solicitud con un error 403 si no es admin.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return $next($request);
        }

        abort(403, 'No tienes permiso para acceder a esta página.');
    }
}
