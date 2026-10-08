<?php

namespace App\Http\Middleware;

use App\Models\Master\Tenant;
use App\Models\Master\TenantUserLookup;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        // Master Admin uses the master DB directly — skip tenant logic
        if ($request->routeIs('master.*')) {
            return $next($request);
        }

        $tenantId = session('tenant_id');
        $tenantUserLookup = null;

        if (!$tenantId) {
            $authenticatedUser = Auth::guard('web')->user();

            if ($authenticatedUser) {
                $tenantUserLookup = TenantUserLookup::on('master')
                    ->where('email', $authenticatedUser->email)
                    ->first();
                $tenantId = $tenantUserLookup?->tenant_id;
            }

            if (!$tenantId) {
                if ($authenticatedUser) {
                    Auth::guard('web')->logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->with('error', 'Your tenant session has expired. Please log in again.');
                }

                return $next($request);
            }
        }

        $tenant = Tenant::on('master')->find($tenantId);

        // Tenant not found or deactivated → force logout
        if (!$tenant || !$tenant->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Please contact the administrator.');
        }

        TenantService::setTenant($tenant);

        if ($tenantUserLookup) {
            $request->session()->put(Auth::guard('web')->getName(), $tenantUserLookup->user_id);
            $request->session()->put('tenant_id', $tenant->id);
            Auth::guard('web')->forgetUser();
            $request->setUserResolver(fn () => Auth::guard('web')->user());
        }

        return $next($request);
    }
}
