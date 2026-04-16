<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrientationQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'orientation_id',
        'question_text',
    ];

    public function orientation()
    {
        return $this->belongsTo(Orientation::class);
    }

    public function options()
    {
        return $this->hasMany(OrientationOption::class, 'question_id');
    }
}
