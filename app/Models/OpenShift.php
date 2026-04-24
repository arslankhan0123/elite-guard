<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'date',
        'shift_name',
        'start_time',
        'end_time',
        'slots',
        'notes',
        'status',
    ];

    /**
     * Get the site for this open shift.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get all claims for this open shift.
     */
    public function claims()
    {
        return $this->hasMany(OpenShiftClaim::class);
    }

    /**
     * Get approved claims count.
     */
    public function getApprovedCountAttribute(): int
    {
        return $this->claims()->where('status', 'approved')->count();
    }

    /**
     * Check if the open shift is full (all slots taken).
     */
    public function getIsFullAttribute(): bool
    {
        return $this->claims()->where('status', 'approved')->count() >= $this->slots;
    }
}
