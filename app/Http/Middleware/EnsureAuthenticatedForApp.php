<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticatedForApp
{
    /**
     * Require authentication for routes under the application area (/app/...),
     * except paths listed in config('app_access.guest_paths').
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('app_access.enforce', false)) {
            return $next($request);
        }

        if (! $this->isAppAreaRequest($request)) {
            return $next($request);
        }

        if (Auth::check()) {
            return $next($request);
        }

        if ($this->matchesGuestPath($request)) {
            return $next($request);
        }

        return redirect()->guest(route('login'));
    }

    protected function isAppAreaRequest(Request $request): bool
    {
        return $request->is('app', 'app/*', '*/app', '*/app/*');
    }

    protected function matchesGuestPath(Request $request): bool
    {
        $rules = config('app_access.guest_paths', []);

        foreach ($rules as $rule) {
            $methods = $rule['methods'] ?? ['GET'];
            if (! in_array($request->method(), $methods, true)) {
                continue;
            }

            $patterns = $rule['patterns'] ?? [];
            if ($patterns === []) {
                continue;
            }

            if ($request->is($patterns)) {
                return true;
            }
        }

        return false;
    }
}
