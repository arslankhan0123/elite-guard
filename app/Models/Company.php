<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
