<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Tenant;
use App\Models\Master\TenantUserLookup;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    // Monthly rate in CAD
    const MONTHLY_RATE = 350.00;

    public function index()
    {
        $tenants = Tenant::on('master')->latest()->paginate(15);
        return view('master.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('master.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'admin_email'         => 'required|email|max:255',
            'admin_password'      => 'required|string|min:8',
            'phone'               => 'nullable|string|max:50',
            'notes'               => 'nullable|string|max:1000',
            'subscription_type'   => 'required|in:trial,monthly,yearly',
            'subscription_period' => 'required_if:subscription_type,trial|nullable|in:1day,2days,3days,1week,2weeks,3weeks,1month',
        ]);

        // Generate unique slug
        $slug = Str::slug($validated['name']);
        $base = $slug;
        $i    = 2;
        while (Tenant::on('master')->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        $dbName = Tenant::dbNameFromSlug($slug);

        // Check email uniqueness
        if (Tenant::on('master')->where('admin_email', $validated['admin_email'])->exists()) {
            return back()->withErrors(['admin_email' => 'This email is already assigned to another tenant.'])->withInput();
        }

        // Compute subscription fields
        $subType   = $validated['subscription_type'];
        $subPeriod = $subType === 'trial' ? $validated['subscription_period'] : null;
        $startsAt  = now();
        $endsAt    = Tenant::computeEndsAt($subType, $subPeriod, $startsAt);
        $amount    = match ($subType) {
            'monthly' => self::MONTHLY_RATE,
            'yearly'  => round(self::MONTHLY_RATE * 12 * 0.95, 2),
            default   => 0.00,
        };

        // 1. Create the tenant record
        $tenant = Tenant::on('master')->create([
            'name'                   => $validated['name'],
            'slug'                   => $slug,
            'db_name'                => $dbName,
            'admin_email'            => $validated['admin_email'],
            'phone'                  => $validated['phone'] ?? null,
            'is_active'              => true,
            'notes'                  => $validated['notes'] ?? null,
            'subscription_type'      => $subType,
            'subscription_period'    => $subPeriod,
            'subscription_amount'    => $amount,
            'subscription_starts_at' => $startsAt,
            'subscription_ends_at'   => $endsAt,
            'expiry_notified'        => false,
        ]);

        // 2. Create the tenant database
        TenantService::createDatabase($dbName);

        // 3. Run migrations on the tenant DB
        TenantService::runMigrationsForTenant($tenant);

        // 4. Create the SuperAdmin user inside the tenant DB
        $userId = DB::connection('tenant')->table('users')->insertGetId([
            'name'          => $validated['name'] . ' Admin',
            'email'         => $validated['admin_email'],
            'password'      => Hash::make($validated['admin_password']),
            'real_password' => $validated['admin_password'],
            'role'          => 'SuperAdmin',
            'status'        => 1,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        // 5. Sync to lookup table
        TenantUserLookup::on('master')->updateOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $validated['admin_email']],
            ['user_id'   => $userId]
        );

        return redirect()->route('master.tenants.show', $tenant->id)
            ->with('success', "Tenant \"{$tenant->name}\" created successfully with database `{$dbName}`.");
    }

    public function show(Tenant $tenant)
    {
        // Count users in the tenant's own DB
        try {
            TenantService::setTenant($tenant);
            $userCount = DB::connection('tenant')->table('users')->count();
        } catch (\Throwable $e) {
            $userCount = 'N/A';
        }

        return view('master.tenants.show', compact('tenant', 'userCount'));
    }

    public function edit(Tenant $tenant)
    {
        return view('master.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'admin_email'         => 'required|email|max:255',
            'admin_password'      => 'nullable|string|min:8',
            'phone'               => 'nullable|string|max:50',
            'notes'               => 'nullable|string|max:1000',
            'is_active'           => 'nullable|boolean',
            'subscription_type'   => 'required|in:trial,monthly,yearly',
            'subscription_period' => 'required_if:subscription_type,trial|nullable|in:1day,2days,3days,1week,2weeks,3weeks,1month',
        ]);

        // Check email uniqueness against other tenants
        if (Tenant::on('master')
            ->where('admin_email', $validated['admin_email'])
            ->where('id', '!=', $tenant->id)
            ->exists()
        ) {
            return back()->withErrors(['admin_email' => 'This email is already used by another tenant.'])->withInput();
        }

        $oldEmail = $tenant->admin_email;

        // Recompute subscription only if plan changed
        $newSubType   = $validated['subscription_type'];
        $newSubPeriod = $newSubType === 'trial' ? $validated['subscription_period'] : null;
        $subChanged   = $newSubType   !== $tenant->subscription_type
                     || $newSubPeriod !== $tenant->subscription_period;

        $startsAt = $subChanged ? now() : ($tenant->subscription_starts_at ?? now());
        $endsAt   = $subChanged
            ? Tenant::computeEndsAt($newSubType, $newSubPeriod, $startsAt)
            : $tenant->subscription_ends_at;
        $amount = match ($newSubType) {
            'monthly' => self::MONTHLY_RATE,
            'yearly'  => round(self::MONTHLY_RATE * 12 * 0.95, 2),
            default   => 0.00,
        };

        $tenant->update([
            'name'                   => $validated['name'],
            'admin_email'            => $validated['admin_email'],
            'phone'                  => $validated['phone'] ?? null,
            'notes'                  => $validated['notes'] ?? null,
            'is_active'              => $request->boolean('is_active'),
            'subscription_type'      => $newSubType,
            'subscription_period'    => $newSubPeriod,
            'subscription_amount'    => $amount,
            'subscription_starts_at' => $startsAt,
            'subscription_ends_at'   => $endsAt,
            'expiry_notified'        => $subChanged ? false : $tenant->expiry_notified,
        ]);

        // If admin email changed, update lookup table
        if ($oldEmail !== $validated['admin_email']) {
            TenantUserLookup::on('master')
                ->where('tenant_id', $tenant->id)
                ->where('email', $oldEmail)
                ->update(['email' => $validated['admin_email']]);
        }

        // Reset admin password if provided
        if (!empty($validated['admin_password'])) {
            try {
                TenantService::setTenant($tenant);
                DB::connection('tenant')->table('users')
                    ->where('email', $validated['admin_email'])
                    ->update([
                        'password'      => Hash::make($validated['admin_password']),
                        'real_password' => $validated['admin_password'],
                        'updated_at'    => now(),
                    ]);
            } catch (\Throwable $e) {
                // Log and continue
            }
        }

        return redirect()->route('master.tenants.show', $tenant->id)
            ->with('success', 'Tenant updated successfully.');
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenant->update(['is_active' => !$tenant->is_active]);

        $status = $tenant->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Tenant \"{$tenant->name}\" has been {$status}.");
    }
}
