<?php

use App\Helpers\Order;
use Illuminate\Support\Arr;
use Illuminate\Database\Seeder;

class PurchasesTableSeeder extends Seeder
{
    protected function create($allItems, $company, $uId, $vId)
    {
        factory('App\Purchase', mt_rand(1, 5))->make(['user_id' => $uId, 'vendor_id' => $vId])->each(function ($purchase) use ($company, $allItems) {
            $items = $allItems->random(mt_rand(1, 5));
            $total = $total_items = $product_tax_amount = 0;
            $purchaseItems = [];
            $vendor = \App\Vendor::find($purchase->vendor_id);
            foreach ($items as $item) {
                $qty = mt_rand(1, 3);
                $applicable_taxes = $item->taxes->filter(function ($tax) use ($company, $vendor) {
                    if ($tax->state) {
                        $check = $vendor->state == $company->state;
                        return $tax->same ? $check : !$check;
                    }
                    return true;
                });
                $taxes = Order::calculateTaxes($applicable_taxes, $item->cost, $qty);
                $purchaseItem = factory('App\PurchaseItem')->make([
                    'product_id'       => $item->id,
                    'qty'              => $qty,
                    'cost'             => $item->cost,
                    'net_cost'         => $item->cost,
                    'tax_amount'       => $tax = $taxes->isEmpty() ? 0 : $taxes->sum('amount'),
                    'total_tax_amount' => formatDecimal($tax * $qty, 2),
                    'unit_cost'        => $unit_cost = $item->cost + $tax,
                    'subtotal'         => ($unit_cost * $qty),
                    'taxes'            => $taxes,
                    'item_taxes'       => Arr::pluck($item->taxes, 'id'),
                ]);
                $product_tax_amount += $purchaseItem->total_tax_amount;
                $total += $purchaseItem->subtotal;
                $purchaseItems[] = $purchaseItem;
            }

            $v['total'] = $total;
            $v['total_tax_amount'] = $v['product_tax_amount'] = $product_tax_amount;
            $v['grand_total'] = formatDecimal($total, 2);
            $v['recoverable_tax_amount'] = 0;
            $v['recoverable_tax_calculated_on'] = 0;
            foreach ($purchaseItems as $item) {
                $rT = Order::calcRecoverableTaxAmount($item['taxes']);
                $v['recoverable_tax_amount'] += $rT['recoverable_tax_amount'];
                $v['recoverable_tax_calculated_on'] += $rT['recoverable_tax_calculated_on'];
            }
            $purchase->fill($v)->save();
            foreach ($purchaseItems as $item) {
                $taxes = $item->taxes;
                unset($item->taxes);
                $purchase->items()->save($item);
                if ($taxes->isNotEmpty()) {
                    $item->taxes()->sync($taxes->toArray());
                }
            }
            if (!$purchase->draft) {
                if (mt_rand(0, 1)) {
                    $purchase->vendor->payments()->create(
                        factory('App\Payment')->make([
                            'gateway'     => '',
                            'purchase_id' => $purchase->id,
                            'amount'      => $purchase->grand_total,
                        ])->toArray()
                    );
                }
            }
        });
    }

    public function run()
    {
        echo "Creating Purchases\n";
        $allItems = \App\Product::all();
        $company  = \App\Company::first();
        for ($i = 1; $i <= mt_rand(1, 20); $i++) {
            $uId = mt_rand(1, 3);
            \Auth::loginUsingId($uId);
            for ($r = 1; $r <= 2; $r++) {
                $this->create($allItems, $company, $uId, 1);
            }
            $this->create($allItems, $company, $uId, mt_rand(1, 30));
        }
    }
}
