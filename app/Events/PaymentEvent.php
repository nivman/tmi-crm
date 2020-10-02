<?php

namespace App\Events;

use App\Payment;
use Illuminate\Queue\SerializesModels;

class PaymentEvent
{
    use SerializesModels;

    public $deleting;
    public $payment;
    public $updating;

    public function __construct(Payment $payment, $updating = false, $deleting = false)
    {
        $this->payment  = $payment;
        $this->updating = $updating;
        $this->deleting = $deleting;
    }
}
