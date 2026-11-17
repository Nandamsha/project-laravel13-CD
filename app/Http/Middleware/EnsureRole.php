<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Pemakaian di route: EnsureRole::class.':admin' atau EnsureRole::class.':seller' */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();

        abort_unless($user && $user->role === $role && $user->status === 'approved', 403);

        return $next($request);
    }
}
