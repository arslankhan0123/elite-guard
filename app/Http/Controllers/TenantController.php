<?php

namespace App\Http\Controllers;

use App\Mail\TenantWelcomeMail;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * List all tenants.
     */
    public function index()
    {
        $tenants = Tenant::latest()->paginate(15);
        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the create form.
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a new tenant, create SuperAdmin user, and send welcome email.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email|unique:users,email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'plan' => 'required|in:basic,standard,premium',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:2000',
            'subscription_starts_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date|after_or_equal:subscription_starts_at',
        ]);

        $tenant = Tenant::create($validated);

        // 🔑 Auto-generate password and create User with role = SuperAdmin
        $plainPassword = Str::random(10);

        User::create([
            'tenant_id' => $tenant->id,
            'name' => $tenant->contact_person,
            'email' => $tenant->email,
            'password' => Hash::make($plainPassword),
            'real_password' => $plainPassword,
            'role' => 'SuperAdmin',
            'email_verified_at' => now(),
            'status' => $tenant->status === 'active' ? 1 : 0,
        ]);

        // Send welcome email notification with credentials
        try {
            Mail::to($tenant->email)->send(new TenantWelcomeMail($tenant, true, $plainPassword));
        } catch (\Exception $e) {
            logger()->error('Tenant welcome email failed: ' . $e->getMessage());
        }

        return redirect()->route('tenants.index')
            ->with('success', "Tenant '{$tenant->company_name}' and SuperAdmin user account created successfully. Welcome email sent to {$tenant->email}.");
    }

    /**
     * Show edit form.
     */
    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the tenant record and sync user.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $oldEmail = $tenant->email;

        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email,' . $tenant->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'plan' => 'required|in:basic,standard,premium',
            'status' => 'required|in:active,inactive,suspended',
            'notes' => 'nullable|string|max:2000',
            'subscription_starts_at' => 'nullable|date',
            'subscription_ends_at' => 'nullable|date|after_or_equal:subscription_starts_at',
        ]);

        $tenant->update($validated);

        // Sync corresponding SuperAdmin user record
        $user = User::where('email', $oldEmail)->first();
        if ($user) {
            $user->update([
                'name' => $tenant->contact_person,
                'email' => $tenant->email,
                'status' => $tenant->status === 'active' ? 1 : 0,
            ]);
        }

        // Send update notification email
        try {
            Mail::to($tenant->email)->send(new TenantWelcomeMail($tenant, false));
        } catch (\Exception $e) {
            logger()->error('Tenant update email failed: ' . $e->getMessage());
        }

        return redirect()->route('tenants.index')
            ->with('success', "Tenant '{$tenant->company_name}' updated successfully.");
    }

    /**
     * Delete the tenant and associated SuperAdmin user.
     */
    public function destroy(Tenant $tenant)
    {
        $name = $tenant->company_name;
        $email = $tenant->email;

        $tenant->delete();
        User::where('email', $email)->where('role', 'SuperAdmin')->delete();

        return redirect()->route('tenants.index')
            ->with('success', "Tenant '{$name}' and associated SuperAdmin account have been deleted.");
    }
}
