<?php

namespace App\Observers;

use App\PurchaseItem;

class PurchaseItemObserver
{
    public function created(PurchaseItem $item)
    {
        if (stock() && !$item->purchase->draft) {
            if ($item->stock) {
                $item->stock->update(['qty' => $item->stock->qty + $item->qty]);
            }
        }
    }

    public function deleting(PurchaseItem $item)
    {
        $item->taxes()->detach();
        if (stock() && !$item->purchase->draft) {
            if ($item->stock) {
                $item->stock->update(['qty' => $item->stock->qty - $item->qty]);
            }
        }
    }
}
