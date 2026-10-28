<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFarmerIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isFarmer()) {
            $profile = $user->farmerProfile;
            if (!$profile || !$profile->is_approved) {
                if (!$request->routeIs('farmer.pending') && !$request->routeIs('farmer.profile.edit') && !$request->routeIs('farmer.profile.update') && !$request->routeIs('logout')) {
                    return redirect()->route('farmer.pending');
                }
            }
        }

        return $next($request);
    }
}
