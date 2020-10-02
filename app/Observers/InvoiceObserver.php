<?php

namespace App\Observers;

use App\Invoice;

class InvoiceObserver
{
    public function created(Invoice $invoice)
    {
        if (!$invoice->draft) {
            $balance = $invoice->customer->journal->getBalanceInDollars();

            $ajt = $invoice->customer->journal
                ->creditDollars($invoice->grand_total, 'invoice_created')
                ->referencesObject($invoice);
            $invoice->disableLogging();
            $invoice->update(['transaction_id' => $ajt->id]);
            if (!$invoice->recurring_id && $balance < 0) {
                event(new \App\Events\InvoiceEvent($invoice));
            }
            if (!demo() && $invoice->customer->email) {
                try {
                    \Mail::to($invoice->customer->email)->send(new \App\Mail\InvoiceCreated($invoice));
                } catch (\Exception $e) {
                    \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                }
            }
        }
    }

    public function deleting(Invoice $invoice)
    {
        $invoice->items->each->delete();
        $invoice->taxes()->detach();
        $invoice->customer->journal
            ->debitDollars($invoice->grand_total, 'invoice_deleting')
            ->referencesObject($invoice);
    }

    public function updating(Invoice $invoice)
    {
        if (!$invoice->wasRecentlyCreated) {
            if ($invoice->getOriginal('draft') && !$invoice->draft) {
                $balance = $invoice->customer->journal->getBalanceInDollars();

                $ajt = $invoice->customer->journal
                    ->creditDollars($invoice->grand_total, 'invoice_created')
                    ->referencesObject($invoice);
                $temp = $invoice->getEventDispatcher();
                $invoice->unsetEventDispatcher();
                $invoice->update(['transaction_id' => $ajt->id]);
                $invoice->setEventDispatcher($temp);
                if (!$invoice->recurring_id && $balance < 0) {
                    event(new \App\Events\InvoiceEvent($invoice));
                }
                if (!demo() && $invoice->customer->email) {
                    try {
                        \Mail::to($invoice->customer->email)->send(new \App\Mail\InvoiceCreated($invoice));
                    } catch (\Exception $e) {
                        \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                    }
                }
            } elseif (!$invoice->draft && !$invoice->getOriginal('draft') && ($invoice->grand_total != $invoice->getOriginal('grand_total'))) {
                $invoice->customer->journal
                    ->debitDollars($invoice->getOriginal('grand_total'), 'invoice_updating')
                    ->referencesObject($invoice);

                $invoice->customer->journal
                    ->creditDollars($invoice->grand_total, 'invoice_updated')
                    ->referencesObject($invoice);
            }
        }
    }
}
