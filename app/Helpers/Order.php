<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

class Order
{
    public static function prepareData($v, $request, $uId = false, $purchase = false)
    {
        $items = collect($v['products'])->transform(function ($item) use ($purchase) {
            $item['product_id'] = $item['product_id'];
            if ($purchase) {
                $item['discount_amount'] = self::calculateDiscount($item['discount'], $item['cost']);
                $item['total_discount_amount'] = formatDecimal($item['discount_amount'] * $item['qty']);
                $item['net_cost'] = $item['cost'] - $item['discount_amount'];
                $item['taxes'] = self::calculateTaxes($item['taxes'], $item['net_cost'], $item['qty']);
                $item['tax_amount'] = $item['taxes'] ? formatDecimal($item['taxes']->sum('amount')) : 0;
                $item['total_tax_amount'] = formatDecimal($item['tax_amount'] * $item['qty']);
                $item['unit_cost'] = formatDecimal($item['net_cost'] + $item['tax_amount']);
                $item['subtotal'] = formatDecimal(($item['qty'] * $item['unit_cost']), 2);
            } else {
                $item['discount_amount'] = self::calculateDiscount($item['discount'], $item['price']);
                $item['total_discount_amount'] = formatDecimal($item['discount_amount'] * $item['qty']);
                $item['net_price'] = $item['price'] - $item['discount_amount'];
                $item['taxes'] = self::calculateTaxes($item['taxes'], $item['net_price'], $item['qty']);
                $item['tax_amount'] = $item['taxes'] ? formatDecimal($item['taxes']->sum('amount')) : 0;
                $item['total_tax_amount'] = formatDecimal($item['tax_amount'] * $item['qty']);
                $item['unit_price'] = formatDecimal($item['net_price'] + $item['tax_amount']);
                $item['subtotal'] = formatDecimal(($item['qty'] * $item['unit_price']), 2);
            }
            $item['item_taxes'] = isset($item['item_taxes']) ? $item['item_taxes'] : Arr::pluck($item['allTaxes'], 'id');
            return $item;
        });

        if ($uId) {
            $v['user_id'] = optional(auth()->user())->id;
        }

        $v['items'] = $items;
        $v['total'] = formatDecimal($v['items']->sum('subtotal'), 2);
        $v['product_tax_amount'] = formatDecimal($items->sum('total_tax_amount'), 2);
        $v['discount_amount'] = self::calculateDiscount($request->discount, $v['total']);
        $v['taxes'] = self::calculateTaxes($request->taxes, ($v['total'] - $v['discount_amount']));
        $v['order_tax_amount'] = $v['taxes'] ? formatDecimal($v['taxes']->sum('amount'), 2) : 0;
        $v['total_tax_amount'] = formatDecimal($v['product_tax_amount'] + $v['order_tax_amount'], 2);
        $v['grand_total'] = formatDecimal($v['total'] - $v['discount_amount'] + $v['order_tax_amount'], 2);

        $rT = self::calcRecoverableTaxAmount($v['taxes']);
        $v['recoverable_tax_amount'] = $rT['recoverable_tax_amount'];
        $v['recoverable_tax_calculated_on'] = $rT['recoverable_tax_calculated_on'];
        foreach ($items as $item) {
            $rT = self::calcRecoverableTaxAmount($item['taxes']);
            $v['recoverable_tax_amount'] += $rT['recoverable_tax_amount'];
            $v['recoverable_tax_calculated_on'] += $rT['recoverable_tax_calculated_on'];
        }

        // Upload attachment
        // if ($request->file('attachment')) {
        //     $media = self::getMedia($request->file('attachment'), 'invoices');
        //     $invoice->attachMedia($media, 'attachment');
        // }

        return $v;
    }

    public static function calculateTaxes($taxes, $amount, $qty = 1)
    {
        if (empty($taxes)) {
            return null;
        }

        $data = [];
        $compoundTaxes = [];
        $totalNonCompoundTaxAmount = 0;
        foreach ($taxes as $tax) {
            if ($tax['compound']) {
                $compoundTaxes[] = $tax;
            } else {
                $calculated_on = $amount;
                $taxAmount = formatDecimal($amount * $tax['rate'] / 100);
                $totalNonCompoundTaxAmount += $taxAmount;
                $data[$tax['id']] = ['calculated_on' => $calculated_on, 'amount' => $taxAmount, 'total_amount' => formatDecimal($taxAmount * $qty), 'recoverable' => $tax['recoverable']];
            }
        }
        foreach ($compoundTaxes as $tax) {
            $calculated_on = $amount + $totalNonCompoundTaxAmount;
            $taxAmount = formatDecimal($calculated_on * $tax['rate'] / 100);
            $data[$tax['id']] = ['calculated_on' => $calculated_on, 'amount' => $taxAmount, 'total_amount' => formatDecimal($taxAmount * $qty), 'recoverable' => $tax['recoverable']];
        }

        return collect($data);
    }

    public static function calcRecoverableTaxAmount($taxes)
    {
        $fT = $taxes ? $taxes->where('recoverable', true) : null;
        if (!empty($fT)) {
            return ['recoverable_tax_amount' => $fT->sum('total_amount'), 'recoverable_tax_calculated_on' => $fT->sum('calculated_on')];
        }
        return ['recoverable_tax_amount' => 0, 'recoverable_tax_calculated_on' => 0];
    }

    public static function calculateDiscount($discount = 0, $amount = 0)
    {
        if ($discount && $amount) {
            $dis_pos = strpos($discount, '%');
            if ($dis_pos !== false) {
                $dv = explode('%', $discount);
                $discount = formatDecimal((($amount * (float) $dv[0]) / 100), 2);
            }
        }
        return $discount;
    }

    public static function orderTransaction($v, $class, $order = null)
    {
        return \DB::transaction(function () use ($v, $class, $order) {
            if ($order) {
                $order->update($v);
                $order->items->each->delete();
                $order->taxes()->detach();
            } else {
                $order = $class::create($v);
            }
            foreach ($v['items'] as $item) {
                $ii = $order->items()->create($item);
                if ($item['taxes'] && $item['taxes']->isNotEmpty()) {
                    $ii->taxes()->sync($item['taxes']->toArray());
                }
            }
            if ($v['taxes'] && $v['taxes']->isNotEmpty()) {
                $order->taxes()->sync($v['taxes']->toArray());
            }
            return $order;
        });
    }
}
