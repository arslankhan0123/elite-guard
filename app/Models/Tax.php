<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'description',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
