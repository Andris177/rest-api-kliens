<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('api_token')) {
            return redirect()
                ->route('login')
                ->with('error', 'Jelentkezz be a módosításhoz.');
        }

        return $next($request);
    }
}
