<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'title',
        'summary',
        'company_id',
        'site_id',
        'invoice_date',
        'due_date',
        'po_so_number',
        'subtotal',
        'tax_total',
        'total',
        'amount_due',
        'notes',
        'status',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_due' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function generateNextInvoiceNumber(): string
    {
        $latest = self::orderBy('id', 'desc')->first();
        if (!$latest) {
            return '0000001';
        }

        $numberPart = preg_replace('/[^0-9]/', '', $latest->invoice_number);
        if ($numberPart !== '' && is_numeric($numberPart)) {
            $next = (int) $numberPart + 1;
            return str_pad((string) $next, 7, '0', STR_PAD_LEFT);
        }

        return '0000001';
    }
}
