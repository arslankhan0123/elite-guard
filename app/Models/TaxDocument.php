<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxDocument extends Model
{
    use HasFactory;

    const TYPES = ['Td1-Fill', 'Td1-Ab', 'Td2-Ab'];

    protected $fillable = [
        'type',
        'file_path',
    ];

    public function submissions()
    {
        return $this->hasMany(TaxDocumentSubmission::class, 'tax_document_id');
    }
}
