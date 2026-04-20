<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RequireRoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!in_array($request->user()?->role, $roles, true)) {
            throw new AccessDeniedHttpException('Acceso denegado.');
        }

        return $next($request);
    }
}