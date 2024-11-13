<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {

        if (auth()->user()->hasRole('Super Admin')) {
            return $next($request);
        }
        if (!auth()->user()->hasPermission($permission)) {
            if ($request->wantsJson() || $request->inertia()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
