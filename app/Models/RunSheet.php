<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'site_id',
        'date',
        'run_sheet_name',
        'start_time',
        'end_time',
        'duration',
        'job_type',
        'sequence',
    ];

    /**
     * Get the user that owns the run sheet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the site assigned in this run sheet.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
