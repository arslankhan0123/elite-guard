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
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $token = JWTAuth::getToken();

            if ($token) {
                $payload  = JWTAuth::decode($token);
                $tenantId = $payload->get('tenant_id');

                if ($tenantId) {
                    $tenant = Tenant::on('master')->find($tenantId);

                    // Tenant not found or deactivated → reject immediately
                    if (!$tenant) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Account not found. Please contact the administrator.',
                        ], 403);
                    }

                    if (!$tenant->is_active) {
                        return response()->json([
                            'status'  => false,
                            'message' => 'Your account has been deactivated. Please contact the administrator.',
                        ], 403);
                    }

                    TenantService::setTenant($tenant);
                }
            }
        } catch (\Throwable $e) {
            // No token or invalid token — auth:api will reject it downstream
        }

        return $next($request);
    }
}
