<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostEsc extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'subject',
        'long_description',
        'pdf_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
