<?php

namespace App\Listeners;

use App\Payment;
use App\Events\PurchaseEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckPaymentsForPurchase implements ShouldQueue
{
    public function failed(PurchaseEvent $event, $exception)
    {
        \Log::error('PurchaseEvent failed!', ['purchase_id' => $event->purchase->id, 'Error' => $exception]);
    }

    public function handle(PurchaseEvent $event)
    {
        $payments   = Payment::received()->ofVendor($event->purchase->vendor_id)->latest()->limit(10)->get();
        $pay_amount = $event->purchase->grand_total;
        foreach ($payments as $payment) {
            $used = $payment->purchases->each->pivot->sum('amount');
            if ($used < $payment->amount) {
                $balance = $payment->amount - $used;
                if ($balance >= $pay_amount) {
                    $event->purchase->payments()->attach($payment->id, ['amount' => $pay_amount]);
                    $event->purchase->update(['paid' => 1]);
                    break;
                } elseif ($balance >= 0 && $pay_amount >= $balance) {
                    $pay_amount -= $balance;
                    $event->purchase->payments()->attach($payment->id, ['amount' => $balance]);
                }
            }
        }
    }
}
