<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'status',
        'document',
        'description',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
