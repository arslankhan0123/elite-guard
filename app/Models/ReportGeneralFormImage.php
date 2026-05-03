<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportGeneralFormImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_general_form_id',
        'observation_image_path',
        'cleared_area_image_path',
    ];

    public function reportGeneralForm()
    {
        return $this->belongsTo(ReportGeneralForm::class);
    }
}
