<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsMasterAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('master')->check()) {
            return redirect()->route('master.login')
                ->with('error', 'Please log in as Master Admin.');
        }

        return $next($request);
    }
}
