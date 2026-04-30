<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'worker_email',
        'shift_date',
        'location',
        'start_time',
        'end_time',
        'client',
        'supervisor_first_name',
        'supervisor_last_name',
        'position_today',
        'compliance_fit_for_duty',
        'any_injuries',
        'physically_prepared',
        'any_symptoms',
        'understand_unethical_work_sick',
        'up_to_date_orders',
        'believe_fit_for_duty',
        'safety_concerns',
        'hazards_identified',
        'right_to_refuse',
        'right_to_participate',
        'signature',
    ];

    protected $casts = [
        'compliance_fit_for_duty' => 'boolean',
        'any_injuries' => 'boolean',
        'physically_prepared' => 'boolean',
        'any_symptoms' => 'boolean',
        'understand_unethical_work_sick' => 'boolean',
        'up_to_date_orders' => 'boolean',
        'believe_fit_for_duty' => 'boolean',
        'safety_concerns' => 'boolean',
        'hazards_identified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
