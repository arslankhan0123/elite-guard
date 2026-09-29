<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'plan',
        'status',
        'notes',
        'subscription_starts_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'subscription_starts_at' => 'datetime',
        'subscription_ends_at'   => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
