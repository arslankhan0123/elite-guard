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
        'compliance_fit_for_duty',
        'any_injuries',
        'physically_prepared',
        'any_symptoms',
        'understand_unethical_work_sick',
        'up_to_date_orders',
        'believe_fit_for_duty',
    ];

    protected $casts = [
        'compliance_fit_for_duty' => 'boolean',
        'any_injuries' => 'boolean',
        'physically_prepared' => 'boolean',
        'any_symptoms' => 'boolean',
        'understand_unethical_work_sick' => 'boolean',
        'up_to_date_orders' => 'boolean',
        'believe_fit_for_duty' => 'boolean',
        'shift_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
