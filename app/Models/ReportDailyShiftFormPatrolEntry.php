<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDailyShiftFormPatrolEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_daily_shift_form_id',
        'time_range',
        'summary',
    ];

    public function reportDailyShiftForm()
    {
        return $this->belongsTo(ReportDailyShiftForm::class);
    }
}
