<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (! $user || ! in_array($user->role->value, $roles) || $user->is_active === false) {
            return response()->json([
                'ok' => false,
                'message' => 'Acesso negado.'
            ], 403);
        }

        return $next($request);
    }
}
