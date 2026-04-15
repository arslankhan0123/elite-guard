<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignedOrientation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'orientation_id',
        'agreed',
        'document',
    ];

    /**
     * Get the user that signed the orientation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the orientation that was signed.
     */
    public function orientation()
    {
        return $this->belongsTo(Orientation::class);
    }
}
