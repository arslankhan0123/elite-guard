<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RunSheetImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'run_sheet_id',
        'image_path',
    ];

    public function runSheet()
    {
        return $this->belongsTo(RunSheet::class);
    }
}
