<?php

namespace App\Listeners;

use App\Payment;
use App\Events\InvoiceEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckPaymentsForInvoice implements ShouldQueue
{
    public function failed(InvoiceEvent $event, $exception)
    {
        \Log::error('InvoiceEvent failed!', ['invoice_id' => $event->invoice->id, 'Error' => $exception]);
    }

    public function handle(InvoiceEvent $event)
    {
        $payments   = Payment::received()->ofCustomer($event->invoice->customer_id)->latest()->limit(10)->get();
        $pay_amount = $event->invoice->grand_total;
        foreach ($payments as $payment) {
            $used = $payment->invoices->each->pivot->sum('amount');
            if ($used < $payment->amount) {
                $balance = $payment->amount - $used;
                if ($balance >= $pay_amount) {
                    $event->invoice->payments()->attach($payment->id, ['amount' => $pay_amount]);
                    $event->invoice->update(['paid' => 1]);
                    break;
                } elseif ($balance >= 0 && $pay_amount >= $balance) {
                    $pay_amount -= $balance;
                    $event->invoice->payments()->attach($payment->id, ['amount' => $balance]);
                }
            }
        }
    }
}
