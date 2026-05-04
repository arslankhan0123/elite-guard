<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeBoard extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'subject',
        'long_description',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
