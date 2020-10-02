<?php

use App\Helpers\Order;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class RecurringsTableSeeder extends Seeder
{
    public function run()
    {
        echo "Creating Recurrings\n";
        $allItems = \App\Product::all();
        $company  = \App\Company::first();
        for ($i = 1; $i <= 6; $i++) {
            $invoice = factory('App\Recurring')->make([
                'user_id'       => 1,
                'customer_id'   => mt_rand(0, 1) ? 1 : mt_rand(1, 30),
                'create_before' => $i - 1,
                'reference'     => 'RECURR0' . $i,
                'repeat'        => $i <= 2 ? 'daily' : 'weekly',
            ]);
            $invoiceItems = [];
            $total        = $total_items        = 0;
            $items        = $allItems->random(mt_rand(1, 5));
            $customer     = \App\Customer::find($invoice->customer_id);
            unset($invoice->date, $invoice->draft);

            foreach ($items as $item) {
                $qty              = mt_rand(1, 3);
                $applicable_taxes = $item->taxes->filter(function ($tax) use ($company, $customer) {
                    if ($tax->state) {
                        $check = $customer->state == $company->state;
                        return $tax->same ? $check : !$check;
                    }
                    return true;
                });
                $taxes       = Order::calculateTaxes($applicable_taxes, $item->price, $qty);
                $invoiceItem = factory('App\RecurringItem')->make([
                    'product_id'       => $item->id,
                    'qty'              => $qty,
                    'price'            => $item->price,
                    'net_price'        => $item->price,
                    'tax_amount'       => $tax = $taxes->isEmpty() ? 0 : $taxes->sum('amount'),
                    'total_tax_amount' => formatDecimal($tax * $qty, 2),
                    'unit_price'       => $unit_price = $item->price + $tax,
                    'subtotal'         => ($unit_price * $qty),
                    'taxes'            => $taxes,
                    'item_taxes'       => Arr::pluck($item->taxes, 'id'),
                ]);
                $total += $invoiceItem->subtotal;
                $invoiceItems[] = $invoiceItem;
            }

            $product_tax_amount = formatDecimal(collect($invoiceItems)->sum('total_tax_amount'), 2);
            $discount_amount    = Order::calculateDiscount(0, $total);
            $order_taxes        = Order::calculateTaxes(0, ($total - $discount_amount));
            $order_tax_amount   = $order_taxes ? formatDecimal($order_taxes->sum('amount'), 2) : 0;
            $total_tax_amount   = formatDecimal($product_tax_amount + $order_tax_amount, 2);
            $grand_total        = formatDecimal($total - $discount_amount + $order_tax_amount, 2);
            $invoice->fill(compact('total', 'product_tax_amount', 'total_tax_amount', 'grand_total'));

            $invoice = \App\Recurring::create($invoice->toArray());
            foreach ($invoiceItems as $item) {
                $taxes = $item->taxes;
                unset($item->taxes);
                $invoice->items()->save($item);
                if ($taxes->isNotEmpty()) {
                    $item->taxes()->sync($taxes->toArray());
                }
            }
        }
    }
}
