<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    // Get all sites
    public function getUser()
    {
        $user = User::with(['candidate', 'bankDetail', 'licenseDetail', 'availability', 'officeDetail'])->find(Auth::id());
        $data = [
            'status' => true,
            'message' => 'User retrieved successfully',
            'user' => $user
        ];
        return $data;
    }

    public function userUpdate($request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($request->has('name')) {
            $user->update(['name' => $request->name]);
        } 

        if ($request->has('email')) {
            $user->update(['email' => $request->email]);
        }

        if ($request->has('password')) {
            $user->update([
                'real_password' => $request->password,
                'password' => Hash::make($request->password)
            ]);
        }

        $user = User::with(['candidate', 'bankDetail', 'licenseDetail', 'availability', 'officeDetail'])->find($user->id);
        $data = [
            'status' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ];
        return $data;
    }
}
