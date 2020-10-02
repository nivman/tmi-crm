<?php

namespace App\Observers;

use App\Purchase;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        if (!$purchase->draft) {
            $balance = $purchase->vendor->journal->getBalanceInDollars();

            $ajt = $purchase->vendor->journal->creditDollars(
                $purchase->grand_total,
                'purchase_created'
            )->referencesObject($purchase);
            $purchase->disableLogging();
            $purchase->update(['transaction_id' => $ajt->id]);
            if ($balance < 0) {
                event(new \App\Events\PurchaseEvent($purchase));
            }
            if (!demo() && $purchase->vendor->email) {
                try {
                    \Mail::to($purchase->vendor->email)->send(new \App\Mail\PurchaseCreated($purchase));
                } catch (\Exception $e) {
                    \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                }
            }
        }
    }

    public function deleting(Purchase $purchase)
    {
        $purchase->items->each->delete();
        $purchase->taxes()->detach();
        $purchase->vendor->journal->debitDollars(
            $purchase->getOriginal('grand_total'),
            'purchase_deleting'
        )->referencesObject($purchase);
    }

    public function updating(Purchase $purchase)
    {
        if (!$purchase->wasRecentlyCreated) {
            if ($purchase->getOriginal('draft') && !$purchase->draft) {
                $balance = $purchase->vendor->journal->getBalanceInDollars();

                $ajt = $purchase->vendor->journal->creditDollars(
                    $purchase->grand_total,
                    'purchase_created'
                )->referencesObject($purchase);
                $temp = $purchase->getEventDispatcher();
                $purchase->unsetEventDispatcher();
                $purchase->update(['transaction_id' => $ajt->id]);
                $purchase->setEventDispatcher($temp);
                if ($balance < 0) {
                    event(new \App\Events\PurchaseEvent($purchase));
                }
                if (!demo() && $purchase->vendor->email) {
                    try {
                        \Mail::to($purchase->vendor->email)->send(new \App\Mail\PurchaseCreated($purchase));
                    } catch (\Exception $e) {
                        \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
                    }
                }
            } elseif (!$purchase->draft && !$purchase->getOriginal('draft') && ($purchase->grand_total != $purchase->getOriginal('grand_total'))) {
                $purchase->vendor->journal->debitDollars(
                    $purchase->getOriginal('grand_total'),
                    'purchase_updating'
                )->referencesObject($purchase);

                $purchase->vendor->journal->creditDollars(
                    $purchase->grand_total,
                    'purchase_updated'
                )->referencesObject($purchase);
            }
        }
    }
}
