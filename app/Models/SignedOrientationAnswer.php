<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignedOrientationAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'signed_orientation_id',
        'question_id',
        'option_id',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function signedOrientation()
    {
        return $this->belongsTo(SignedOrientation::class);
    }

    public function question()
    {
        return $this->belongsTo(OrientationQuestion::class);
    }

    public function option()
    {
        return $this->belongsTo(OrientationOption::class);
    }
}
