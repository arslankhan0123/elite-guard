<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\ProfileCompletionService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request, ProfileCompletionService $profileCompletion): View
    {
        $timezones = \DateTimeZone::listIdentifiers();
        $currentTimezone = \App\Models\Setting::get('timezone', config('app.timezone'));

        return view('profile.edit', [
            'user' => $request->user(),
            'profileCompletion' => $profileCompletion->calculate($request->user()),
            'timezones' => $timezones,
            'currentTimezone' => $currentTimezone,
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
}
