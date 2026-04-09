<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'institution_number',
        'transit_number',
        'account_number',
        'bank_address',
        'interac_email',
        'void_cheque_file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
