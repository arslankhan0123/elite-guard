<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'email',
        'phone',
        'city',
        'country',
        'address',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'status'    => 'boolean',
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function nfcTags()
    {
        return $this->hasMany(NfcTag::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'site_user')->withPivot('assigned_at')->withTimestamps();
    }
}
