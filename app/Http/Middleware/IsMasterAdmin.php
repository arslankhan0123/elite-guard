<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsMasterAdmin
{
    /**
     * Handle an incoming request.
     * Only MasterAdmin users can pass through.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || $user->role !== 'MasterAdmin') {
            abort(403, 'Unauthorized. MasterAdmin access only.');
        }

        return $next($request);
    }
}
