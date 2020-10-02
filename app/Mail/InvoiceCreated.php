<?php

namespace App\Mail;

use App\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        return $this->subject('New Invoice Created!')
            ->view('mail.invoice.created')
            ->with([
                'company' => $this->invoice->company,
                'invoice_number' => $this->invoice->id,
                'grand_total' => $this->invoice->grand_total,
                'customer_name' => $this->invoice->customer->name,
                'customer_company' => $this->invoice->customer->company,
                'invoice_url' => URL::signedRoute('order', ['act' => 'invoice', 'hash' => $this->invoice->hash]),
            ]);
    }
}
