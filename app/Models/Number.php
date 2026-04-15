<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'designation',
        'label',
        'number',
        'number_with_code',
        'type',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
