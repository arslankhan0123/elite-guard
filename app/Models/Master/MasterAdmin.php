<?php

namespace App\Models\Master;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class MasterAdmin extends Authenticatable
{
    use Notifiable;

    protected $connection = 'master';
    protected $table      = 'master_admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'real_password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
