<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'name',
        'logo',
        'email',
        'phone',
        'website',
        'address',
        'country',
        'city',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }
}
