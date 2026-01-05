<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureApiAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('api_token')) {
            return redirect()->route('login')
                ->with('error', 'A művelethez be kell jelentkezni.');
        }

        return $next($request);
    }
}
