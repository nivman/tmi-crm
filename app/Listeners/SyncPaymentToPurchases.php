<?php

namespace App\Listeners;

use App\Purchase;
use App\Events\PaymentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncPaymentToPurchases implements ShouldQueue
{
    public function failed(PaymentEvent $event, $exception)
    {
        \Log::error('PaymentEvent failed!', ['payment_id' => $event->payment->id, 'Error' => $exception]);
    }

    public function handle(PaymentEvent $event)
    {
        if ($event->deleting) {
            $event->payment->purchases->each(function ($purchase) {
                $purchase->payments()->detach();
                $purchase->update(['paid' => null]);
            });
        } else {
            if ($event->payment->payable instanceof \App\Vendor) {
                if ($event->payment->purchase_id) {
                    $purchase = Purchase::find($event->payment->purchase_id);
                    $purchase->payments()->attach($event->payment->id, ['amount' => $event->payment->amount]);
                    $paid_amount   = $purchase->payments->isEmpty() ? 0 : $purchase->payments->sum('amount');
                    $paying_amount = $purchase->grand_total - $paid_amount;
                    if (!$purchase->paid && $paying_amount <= $event->payment->amount) {
                        $purchase->update(['paid' => 1]);
                    }
                } else {
                    if ($event->updating) {
                        $event->payment->purchases->each(function ($purchase) {
                            $purchase->payments()->detach();
                            $purchase->update(['paid' => null]);
                        });
                    }
                    $purchases = Purchase::active()->unpaid()->ofVendor($event->payment->payable_id)->orderBy('date')->orderBy('id')->get();
                    $balance   = $event->payment->amount;
                    foreach ($purchases as $purchase) {
                        $paid_amount   = $purchase->payments->isEmpty() ? 0 : $purchase->payments->sum('amount');
                        $paying_amount = $purchase->grand_total - $paid_amount;
                        if ($balance == 0) {
                            break;
                        } elseif ($paying_amount > 0 && $paying_amount <= $balance) {
                            $purchase->payments()->attach($event->payment->id, ['amount' => $paying_amount]);
                            $purchase->update(['paid' => 1]);
                            $balance -= $paying_amount;
                        } elseif ($paying_amount > $balance) {
                            $purchase->payments()->attach($event->payment->id, ['amount' => $balance]);
                            break;
                        }
                    }
                }
            }
        }
    }
}
