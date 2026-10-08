<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'company_name', 'tax_id', 'address',
        'city', 'province', 'postal_code', 'country', 'notes',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
