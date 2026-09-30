<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Master\Tenant;
use App\Models\Master\TenantUserLookup;
use App\Services\TenantService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $email = $request->input('email');

        // ── Step 1: Find which tenant this email belongs to ────────────────
        $lookup = TenantUserLookup::on('master')
            ->where('email', $email)
            ->first();

        if (!$lookup) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $tenant = Tenant::on('master')
            ->where('id', $lookup->tenant_id)
            ->where('is_active', true)
            ->first();

        if (!$tenant) {
            throw ValidationException::withMessages([
                'email' => 'Your account is inactive. Please contact the administrator.',
            ]);
        }

        // ── Step 2: Switch to the tenant's database BEFORE authenticating ──
        TenantService::setTenant($tenant);

        // ── Step 3: Now authenticate against the tenant DB ─────────────────
        $request->authenticate();

        if (! in_array(Auth::user()->role, ['SuperAdmin', 'Admin'], true)) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')->with('error', 'Unauthorized access! Only admin users can log into this portal.');
        }

        $request->session()->regenerate();

        // ── Step 4: Store tenant_id in session for subsequent requests ─────
        session(['tenant_id' => $tenant->id]);

        return redirect()->intended(route(Auth::user()->adminLandingRoute(), absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
