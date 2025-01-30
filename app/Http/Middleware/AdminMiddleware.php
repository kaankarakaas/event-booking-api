<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && $request->session()->get('is_admin')) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
