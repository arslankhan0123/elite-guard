<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\ProfileCompletionService;
use App\Services\TenantService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, ProfileCompletionService $profileCompletion): View
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $currentTimezone = \App\Models\Setting::get('timezone', config('app.timezone'));

        $companyLogoPath = \App\Models\Setting::get('company_logo_path', '');

        // Pull company name, email, and phone from the master tenant record (read-only fields)
        $tenant = TenantService::getTenant();
        $companyName  = $tenant ? $tenant->name        : \App\Models\Setting::get('company_name', '');
        $companyEmail = $tenant ? $tenant->admin_email  : \App\Models\Setting::get('company_email', '');
        $companyPhone = $tenant ? ($tenant->phone ?? '') : \App\Models\Setting::get('company_phone', '');

        return view('profile.edit', [
            'user'             => $request->user(),
            'profileCompletion'=> $profileCompletion->calculate($request->user()),
            'timezones'        => $timezones,
            'currentTimezone'  => $currentTimezone,
            'companyName'      => $companyName,
            'companyEmail'     => $companyEmail,
            'companyPhone'     => $companyPhone,
            'companyAddress'   => \App\Models\Setting::get('company_address', ''),
            'companyWebsite'   => \App\Models\Setting::get('company_website', ''),
            'companyLogoPath'  => $companyLogoPath,
            'companyLogoUrl'   => $companyLogoPath
                ? \Illuminate\Support\Facades\Storage::url($companyLogoPath)
                : null,
        ]);
    }

    /**
     * Update system timezone setting.
     */
    public function updateTimezone(Request $request): RedirectResponse
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $request->validate([
            'timezone' => ['required', 'string', 'in:' . implode(',', $timezones)],
        ]);

        \App\Models\Setting::set('timezone', $request->timezone);

        return Redirect::route('profile.edit')->with('status', 'timezone-updated');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update company settings (name, logo, address, website).
     * NOTE: company_email and company_phone are managed by Master Admin and cannot be changed here.
     */
    public function updateCompanySettings(Request $request): RedirectResponse
    {
        $request->validate([
            'company_name'    => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_website' => ['nullable', 'url', 'max:255'],
            'company_logo'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,svg,webp', 'max:2048'],
        ]);

        // Only update name, address, website — email and phone are read-only (from tenant record)
        $keys = ['company_name', 'company_address', 'company_website'];
        foreach ($keys as $key) {
            \App\Models\Setting::set($key, $request->input($key, ''));
        }

        if ($request->hasFile('company_logo')) {
            // Delete old logo file if stored on disk
            $oldLogoPath = \App\Models\Setting::get('company_logo_path');
            if ($oldLogoPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldLogoPath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldLogoPath);
            }

            $file = $request->file('company_logo');
            $filename = 'company_logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('logos', $filename, 'public');

            \App\Models\Setting::set('company_logo_path', $path);
        }

        return Redirect::route('profile.edit')->with('status', 'company-settings-updated');
    }
}
