<?php

namespace App\Http\Middleware;

use App\Models\Master\Tenant;
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
        if (session('master_admin_id')) {
            return $next($request);
        }

        $tenantId = session('tenant_id');

        if (!$tenantId) {
            return $next($request);
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

        return $next($request);
    }
}
