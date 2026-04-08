<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireBackofficeRole
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || $user->role === 'waiter') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        return $next($request);
    }
}