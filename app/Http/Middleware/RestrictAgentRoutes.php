<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictAgentRoutes
{
    /**
     * Routes agents are allowed to access. All other routes redirect to dashboard.
     */
    protected array $allowedForAgent = [
        'dashboard',
        'agent/*',
        'logout',
    ];

    /**
     * Allow agents only dashboard and agent/* (payment-requests) routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check() || auth()->user()->role !== 'agent') {
            return $next($request);
        }

        foreach ($this->allowedForAgent as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        return redirect('/dashboard');
    }
}
