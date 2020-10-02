<?php

namespace App\Events;

use App\Invoice;
use Illuminate\Queue\SerializesModels;

class InvoiceEvent
{
    use SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }
}
