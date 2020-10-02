<?php

namespace App\Observers;

use App\InvoiceItem;

class InvoiceItemObserver
{
    public function created(InvoiceItem $item)
    {
        if (stock() && !$item->invoice->draft) {
            if ($item->stock) {
                $item->stock->update(['qty' => $item->stock->qty - $item->qty]);
            }
        }
    }

    public function deleting(InvoiceItem $item)
    {
        $item->taxes()->detach();
        if (stock() && !$item->invoice->draft) {
            if ($item->stock) {
                $item->stock->update(['qty' => $item->stock->qty + $item->qty]);
            }
        }
    }
}
