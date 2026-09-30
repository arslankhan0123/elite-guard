<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MasterAuthController extends Controller
{
    public function showLogin()
    {
        return view('master.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('master')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $masterAdmin = Auth::guard('master')->user();
            session(['master_admin_id' => $masterAdmin->id]);

            return redirect()->route('master.dashboard');
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('master')->logout();

        $request->session()->forget('master_admin_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('master.login');
    }
}
