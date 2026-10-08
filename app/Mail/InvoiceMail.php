<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Services\CompanyProfileService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $invoice = $this->invoice;
        $companyProfile = app(CompanyProfileService::class)->data();
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice', 'companyProfile'));

        return $this->subject('Invoice #' . $this->invoice->invoice_number . ' from ' . $companyProfile['name'])
                    ->view('emails.invoice', compact('invoice', 'companyProfile'))
                    ->attachData($pdf->output(), 'Invoice_' . $this->invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
