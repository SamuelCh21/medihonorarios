<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // se usa así: ->middleware('role:admin') o ->middleware('role:admin,gerencia')
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->user()?->role, $roles)) {
            return response()->json(['message' => 'No tienes permiso para esto'], 403);
        }

        return $next($request);
    }
}
