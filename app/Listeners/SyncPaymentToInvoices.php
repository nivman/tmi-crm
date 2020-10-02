<?php

namespace App\Listeners;

use App\Invoice;
use App\Events\PaymentEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncPaymentToInvoices implements ShouldQueue
{
    public function failed(PaymentEvent $event, $exception)
    {
        \Log::error('PaymentEvent failed!', ['payment_id' => $event->payment->id, 'Error' => $exception]);
    }

    public function handle(PaymentEvent $event)
    {
        if ($event->deleting) {
            $event->payment->invoices->each(function ($invoice) {
                $invoice->payments()->detach();
                $invoice->update(['paid' => null]);
            });
        } else {
            if ($event->payment->payable instanceof \App\Customer) {
                if ($event->payment->invoice_id) {
                    $invoice = Invoice::find($event->payment->invoice_id);
                    $invoice->payments()->attach($event->payment->id, ['amount' => $event->payment->amount]);
                    $paid_amount   = $invoice->payments->isEmpty() ? 0 : $invoice->payments->sum('amount');
                    $paying_amount = $invoice->grand_total - $paid_amount;
                    if (!$invoice->paid && $paying_amount <= $event->payment->amount) {
                        $invoice->update(['paid' => 1]);
                    }
                } else {
                    if ($event->updating) {
                        $event->payment->invoices->each(function ($invoice) {
                            $invoice->payments()->detach();
                            $invoice->update(['paid' => null]);
                        });
                    }
                    $invoices = Invoice::active()->unpaid()->ofCustomer($event->payment->payable_id)->orderBy('date')->orderBy('id')->get();
                    $balance  = $event->payment->amount;
                    foreach ($invoices as $invoice) {
                        $paid_amount   = $invoice->payments->isEmpty() ? 0 : $invoice->payments->sum('amount');
                        $paying_amount = $invoice->grand_total - $paid_amount;
                        if ($balance == 0) {
                            break;
                        } elseif ($paying_amount > 0 && $paying_amount <= $balance) {
                            $invoice->payments()->attach($event->payment->id, ['amount' => $paying_amount]);
                            $invoice->update(['paid' => 1]);
                            $balance -= $paying_amount;
                        } elseif ($paying_amount > $balance) {
                            $invoice->payments()->attach($event->payment->id, ['amount' => $balance]);
                            break;
                        }
                    }
                }
            }
        }
    }
}
