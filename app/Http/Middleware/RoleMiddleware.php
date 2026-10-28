<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Please sign in to access this page.');
        }

        $user = Auth::user();

        if ($user->status === 'suspended') {
            Auth::logout();
            return redirect()->route('login')->with('danger', 'Your account has been suspended. Please contact support.');
        }

        if (!in_array($user->role, $roles, true)) {
            abort(403, 'Unauthorized access: your account role (' . $user->role . ') does not have permission to view this section.');
        }

        return $next($request);
    }
}
