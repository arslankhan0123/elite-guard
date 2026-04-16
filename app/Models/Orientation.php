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
        'passing_percentage',
    ];

    protected $casts = [
        'status' => 'boolean',
        'passing_percentage' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(OrientationQuestion::class);
    }
}
