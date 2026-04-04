<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    // Get all sites
    public function getUser()
    {
        $user = Auth::user();
        $data = [
            'status' => true,
            'message' => 'User retrieved successfully',
            'user' => $user
        ];
        return $data;
    }
}