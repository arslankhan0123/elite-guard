<?php

namespace App\Http\Middleware;

use App\Models\Master\Tenant;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTenantConnection
{
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for Master Admin — they use the master DB directly
        if (session('master_admin_id')) {
            return $next($request);
        }

        $tenantId = session('tenant_id');
        if ($tenantId) {
            $tenant = Tenant::on('master')->find($tenantId);
            if ($tenant && $tenant->is_active) {
                TenantService::setTenant($tenant);
            }
        }

        return $next($request);
    }
}
