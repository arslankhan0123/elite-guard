<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportIncidentFormImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_incident_form_id',
        'image_path',
    ];

    public function reportIncidentForm()
    {
        return $this->belongsTo(ReportIncidentForm::class);
    }
}
