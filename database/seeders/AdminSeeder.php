<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'arslan.devsspace@gmail.com'], // Unique identifier for the user
            [
                'name' => 'Arslan Khan',
                'password' => Hash::make('Jp7#xV!9rT$eWq3@'),
                'real_password' => 'Jp7#xV!9rT$eWq3@',
                'email_verified_at' => now(),
                'role' => 'SuperAdmin',
            ]
        );
    }
}
