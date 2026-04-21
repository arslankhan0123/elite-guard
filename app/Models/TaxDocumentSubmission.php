<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxDocumentSubmission extends Model
{
    use HasFactory;

    protected $table = 'tax_document_submissions';

    protected $fillable = [
        'user_id',
        'tax_document_id',
        'document_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function taxDocument()
    {
        return $this->belongsTo(TaxDocument::class);
    }
}
