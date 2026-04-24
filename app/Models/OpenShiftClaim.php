<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenShiftClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'open_shift_id',
        'user_id',
        'status',
        'admin_note',
    ];

    /**
     * Get the open shift this claim belongs to.
     */
    public function openShift()
    {
        return $this->belongsTo(OpenShift::class);
    }

    /**
     * Get the user who made the claim.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
