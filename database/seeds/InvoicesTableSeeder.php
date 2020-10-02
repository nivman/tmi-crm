<?php

use App\Helpers\Order;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class InvoicesTableSeeder extends Seeder
{
    protected function create($allItems, $company, $uId, $cId)
    {
        factory('App\Invoice', mt_rand(1, 5))->make(['user_id' => $uId, 'customer_id' => $cId])->each(function ($invoice) use ($allItems, $company) {
            $items = $allItems->random(mt_rand(1, 5));
            $total = $total_items = 0;
            $invoiceItems = [];
            $customer = \App\Customer::find($invoice->customer_id);
            foreach ($items as $item) {
                $qty = mt_rand(1, 3);
                $applicable_taxes = $item->taxes->filter(function ($tax) use ($company, $customer) {
                    if ($tax->state) {
                        $check = $customer->state == $company->state;
                        return $tax->same ? $check : !$check;
                    }
                    return true;
                });
                $taxes = Order::calculateTaxes($applicable_taxes, $item->price, $qty);
                $invoiceItem = factory('App\InvoiceItem')->make([
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

            $v['total'] = $total;
            $v['product_tax_amount'] = formatDecimal(collect($invoiceItems)->sum('total_tax_amount'), 2);
            $discount_amount = Order::calculateDiscount(0, $total);
            $order_taxes = Order::calculateTaxes(0, ($total - $discount_amount));
            $order_tax_amount = $order_taxes ? formatDecimal($order_taxes->sum('amount'), 2) : 0;
            $v['total_tax_amount'] = formatDecimal($v['product_tax_amount'] + $order_tax_amount, 2);
            $v['grand_total'] = formatDecimal($total - $discount_amount + $order_tax_amount, 2);
            $rT = Order::calcRecoverableTaxAmount($invoice->taxes);
            $v['recoverable_tax_amount'] = $rT['recoverable_tax_amount'];
            $v['recoverable_tax_calculated_on'] = $rT['recoverable_tax_calculated_on'];
            foreach ($invoiceItems as $item) {
                $rT = Order::calcRecoverableTaxAmount($item['taxes']);
                $v['recoverable_tax_amount'] += $rT['recoverable_tax_amount'];
                $v['recoverable_tax_calculated_on'] += $rT['recoverable_tax_calculated_on'];
            }

            $invoice->fill($v)->save();
            foreach ($invoiceItems as $item) {
                $taxes = $item->taxes;
                unset($item->taxes);
                $invoice->items()->save($item);
                if ($taxes->isNotEmpty()) {
                    $item->taxes()->sync($taxes->toArray());
                }
            }
            if (!$invoice->draft) {
                if (mt_rand(0, 1)) {
                    $invoice->customer->payments()->create(
                        factory('App\Payment')->make(['amount' => $v['grand_total'], 'invoice_id' => $invoice->id])->toArray()
                    );
                }
            }
        });
    }

    public function run()
    {
        echo "Creating Invoices\n";
        $allItems = \App\Product::all();
        $company  = \App\Company::first();
        for ($i = 1; $i <= 10; $i++) {
        }
        for ($i = 1; $i <= mt_rand(5, 20); $i++) {
            $uId = mt_rand(1, 3);
            \Auth::loginUsingId($uId);
            for ($r = 1; $r <= 2; $r++) {
                $this->create($allItems, $company, $uId, 1);
            }
            $this->create($allItems, $company, $uId, mt_rand(1, 30));
        }
    }
}
