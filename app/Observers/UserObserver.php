<?php

namespace App\Observers;

use App\Models\Master\TenantUserLookup;
use App\Models\User;
use App\Services\TenantService;

class UserObserver
{
    public function saved(User $user): void
    {
        $tenant = TenantService::getTenant();
        if (!$tenant) {
            return;
        }

        TenantUserLookup::on('master')->updateOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $user->email],
            ['user_id'   => $user->id]
        );
    }

    public function deleted(User $user): void
    {
        $tenant = TenantService::getTenant();
        if (!$tenant) {
            return;
        }

        TenantUserLookup::on('master')
            ->where('tenant_id', $tenant->id)
            ->where('email', $user->email)
            ->delete();
    }
}
