<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dispatch extends Model
{
    protected $fillable = [
        'company_id',
        'site_id',
        'assigned_guard_ids',
        'priority',
        'caller_type',
        'caller_name',
        'incident_location',
        'incident_type',
        'incident_date',
        'incident_time',
        'incident_details',
        'action_taken',
        'internal_notes',
        'attachment_path',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'assigned_guard_ids' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class);
    }

    public function getAssignedGuardsAttribute()
    {
        $ids = $this->assigned_guard_ids;
        if (empty($ids) || !is_array($ids)) {
            return collect();
        }
        return User::whereIn('id', $ids)->get();
    }
}
