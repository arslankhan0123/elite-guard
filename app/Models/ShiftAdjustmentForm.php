<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftAdjustmentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_id',
        'current_supervisor_id',
        'supervisor_id',
        'approving_supervisor_id',

        // Employee Information
        'employee_name',
        'employee_id',
        'position_site',
        'department',

        // Current Shift
        'current_date',
        'current_start_time',
        'current_end_time',
        'current_supervisor',
        'current_shift_type',

        // Requested Adjustment - Checkboxes
        'shift_swap',
        'late_start',
        'coverage_request',
        'early_release',
        'time_off_request',
        'overtime_approval',

        // Requested Adjustment - Details
        'requested_date',
        'requested_start_time',
        'requested_end_time',
        'replacement_employee',
        'adjustment_reason',
        'additional_details',

        // Approval Section
        'supervisor_name',
        'approval_date',
        'decision',
        'approved_hours',
        'supervisor_notes',

        // Signatures
        'employee_signature',
        'supervisor_signature',
    ];

    protected $casts = [
        'shift_swap'       => 'boolean',
        'late_start'       => 'boolean',
        'coverage_request' => 'boolean',
        'early_release'    => 'boolean',
        'time_off_request' => 'boolean',
        'overtime_approval' => 'boolean',
        'current_date'     => 'date',
        'requested_date'   => 'date',
        'approval_date'    => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function currentSupervisor()
    {
        return $this->belongsTo(User::class, 'current_supervisor_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function approvingSupervisor()
    {
        return $this->belongsTo(User::class, 'approving_supervisor_id');
    }
}
