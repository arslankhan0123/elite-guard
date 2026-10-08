<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class CompanyProfileService
{
    public function data(): array
    {
        $tenant = TenantService::getTenant();
        $logoPath = Setting::get('company_logo_path');
        $logoFile = $logoPath ? Storage::disk('public')->path($logoPath) : null;

        $hasCustomLogo = $logoFile && is_file($logoFile);

        if (! $hasCustomLogo) {
            $logoFile = public_path('logo.png');
        }

        return [
            'name' => Setting::get('company_name') ?: ($tenant?->name ?: 'Elite Guard Inc.'),
            'email' => $tenant?->admin_email ?: Setting::get('company_email', ''),
            'phone' => $tenant?->phone ?: Setting::get('company_phone', ''),
            'address' => Setting::get('company_address', ''),
            'website' => Setting::get('company_website', ''),
            'logo_url' => $hasCustomLogo ? Storage::disk('public')->url($logoPath) : asset('logo.png'),
            'logo_file' => $logoFile,
        ];
    }
}
