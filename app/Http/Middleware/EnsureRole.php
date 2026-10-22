<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** RBAC sederhana: pakai ->middleware(EnsureRole::class . ':admin') pada route. */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->role !== $role) {
            return redirect()->route($user->role === 'admin' ? 'admin' : 'dashboard');
        }

        return $next($request);
    }
}
