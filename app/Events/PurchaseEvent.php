<?php

namespace App\Events;

use App\Purchase;
use Illuminate\Queue\SerializesModels;

class PurchaseEvent
{
    use SerializesModels;

    public $purchase;

    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }
}
