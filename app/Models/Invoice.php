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
        'customer_id',
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

    public function getCalculatedStatusAttribute(): string
    {
        $statusStr = strtolower($this->status ?? '');
        if ($statusStr === 'draft') {
            return 'draft';
        }
        if ($statusStr === 'paid' || (float) $this->amount_due <= 0) {
            return 'paid';
        }
        if ($this->due_date) {
            $dueDate = \Carbon\Carbon::parse($this->due_date)->startOfDay();
            if ($dueDate->lt(\Carbon\Carbon::today())) {
                return 'overdue';
            }
        }
        return 'active';
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Extract uppercase initials from a company name.
     * e.g. "Avenue Living" → "AL", "Nai Advent" → "NA"
     */
    public static function generateInitials(string $name): string
    {
        $words = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach ($words as $word) {
            if ($word !== '') {
                $initials .= strtoupper(mb_substr($word, 0, 1));
            }
        }
        return $initials ?: 'XX';
    }

    /**
     * Generate next invoice number.
     * With company name  → EG/AL/01, EG/AL/02 …
     * Without company    → EG/GEN/01, EG/GEN/02 …
     */
    public static function generateNextInvoiceNumber(?string $companyName = null): string
    {
        $initials = $companyName ? self::generateInitials($companyName) : 'GEN';
        $prefix   = 'EG/' . $initials . '/';

        $latest = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$latest) {
            return $prefix . '01';
        }

        $numberPart = str_replace($prefix, '', $latest->invoice_number);
        $next = is_numeric($numberPart) ? (int) $numberPart + 1 : 1;

        return $prefix . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }
}
