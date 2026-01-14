<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (auth()->check()) {
            $userRole = auth()->user()->role;
    
            // Admin bypass
            if ($userRole === 'admin' || in_array($userRole, $roles)) {
                return $next($request);
            }
        }
        // dd([
        //     'userRole' => $userRole,
        //     'allowedRoles' => $roles
        // ]);
        abort(403, 'Unauthorized');
    }
}
