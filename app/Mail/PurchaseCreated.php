<?php

namespace App\Mail;

use App\Purchase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    public function build()
    {
        return $this->subject('New Purchase Order!')
            ->view('mail.purchase.created')
            ->with([
                'company' => $this->purchase->company,
                'purchase_number' => $this->purchase->id,
                'grand_total' => $this->purchase->grand_total,
                'vendor_name' => $this->purchase->vendor->name,
                'vendor_company' => $this->purchase->vendor->company,
                'purchase_url' => URL::signedRoute('order', ['act' => 'purchase', 'hash' => $this->purchase->hash]),
            ]);
    }
}
