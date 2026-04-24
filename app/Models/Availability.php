<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'shift',
        'user_notes',
        'admin_notes',
        'status',
    ];

    /**
     * Get the user that owns the availability.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
