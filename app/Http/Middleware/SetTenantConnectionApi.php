<?php

namespace App\Http\Middleware;

use App\Models\Master\Tenant;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class SetTenantConnectionApi
{
    /**
     * Runs BEFORE auth:api — reads the tenant_id custom claim from the JWT
     * payload (without fully authenticating the user) and switches the DB
     * connection so that auth:api finds the user in the right tenant DB.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::getToken();
            if ($token) {
                $payload  = JWTAuth::decode($token);
                $tenantId = $payload->get('tenant_id');

                if ($tenantId) {
                    $tenant = Tenant::on('master')->find($tenantId);
                    if ($tenant && $tenant->is_active) {
                        TenantService::setTenant($tenant);
                    }
                }
            }
        } catch (\Throwable $e) {
            // No token or invalid token — auth:api will reject it downstream
        }

        return $next($request);
    }
}
