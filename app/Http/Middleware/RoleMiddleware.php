<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::guard('admin')->user();
        if (!$user || strtolower($user->role) !== strtolower($role)) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
