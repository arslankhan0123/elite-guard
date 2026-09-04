<?php

namespace App\Mail;

use App\Models\Invoice;
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
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));

        return $this->subject('Invoice #' . $this->invoice->invoice_number . ' from Elite Guard Inc.')
                    ->view('emails.invoice', compact('invoice'))
                    ->attachData($pdf->output(), 'Invoice_' . $this->invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
