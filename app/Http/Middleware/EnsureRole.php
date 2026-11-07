<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $min = collect(UserRole::cases())
            ->first(fn (UserRole $c) => strcasecmp($c->name, $role) === 0);

        abort_unless($min && $request->user()?->hasRole($min), 403);

        return $next($request);
    }
}