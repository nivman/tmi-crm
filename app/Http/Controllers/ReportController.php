<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function customer($request)
    {
        $v = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
            'customer'   => 'required|exists:customers,id',
        ]);

        $v['start_date'] = $v['start_date'] . ' 00:00:00';
        $v['end_date']   = $v['end_date'] . ' 23:59:59';
        $customer        = \App\Customer::find($v['customer']);
        $invoices        = $customer->invoices()->active()->whereBetween('date', [$v['start_date'], $v['end_date']])->get(['grand_total']);
        $due_payments    = $customer->payments()->due()->count();

        $report = [
            'customer'             => $customer,
            'due_payments'         => $due_payments,
            'balance'              => $customer->journal->getBalanceInDollars(),
            'toral_invoice_amount' => formatDecimal($invoices->sum('grand_total')),
        ];

        return ['title' => 'Customer Report', 'heading' => 'Please review the customer report below', 'start_date' => $v['start_date'], 'end_date' => $v['end_date'], 'data' => $report];
    }

    public function general($request)
    {
        $v = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
        ]);

        $v['start_date'] = $v['start_date'] . ' 00:00:00';
        $v['end_date']   = $v['end_date'] . ' 23:59:59';

        $expenses = \App\Expense::whereBetween('created_at', [$v['start_date'], $v['end_date']]);
        $expenses = $expenses->get(['amount']);

        $incomes = \App\Income::whereBetween('created_at', [$v['start_date'], $v['end_date']]);
        $incomes = $incomes->get(['amount']);

        $invoices = \App\Invoice::active()->whereBetween('date', [$v['start_date'], $v['end_date']]);
        $invoices = $invoices->get(['grand_total', 'order_tax_amount', 'product_tax_amount', 'recoverable_tax_amount', 'recoverable_tax_calculated_on']);

        $purchases = \App\Purchase::active()->whereBetween('date', [$v['start_date'], $v['end_date']]);
        $purchases = $purchases->get(['grand_total', 'order_tax_amount', 'product_tax_amount', 'recoverable_tax_amount', 'recoverable_tax_calculated_on']);

        $report = [
            'income'   => formatDecimal($incomes->sum('amount')),
            'expense'  => formatDecimal($expenses->sum('amount')),
            'invoices' => [
                'total'                               => formatDecimal($invoices->sum('grand_total')),
                'total_tax_amount'                    => formatDecimal($invoices->sum('total_tax_amount')),
                'total_order_tax_amount'              => formatDecimal($invoices->sum('order_tax_amount')),
                'total_product_tax_amount'            => formatDecimal($invoices->sum('product_tax_amount')),
                'total_recoverable_tax_amount'        => formatDecimal($invoices->sum('recoverable_tax_amount')),
                'total_recoverable_tax_calculated_on' => formatDecimal($invoices->sum('recoverable_tax_calculated_on')),
            ],
            'purchases' => [
                'total'                               => formatDecimal($purchases->sum('grand_total')),
                'total_tax_amount'                    => formatDecimal($purchases->sum('total_tax_amount')),
                'total_order_tax_amount'              => formatDecimal($purchases->sum('order_tax_amount')),
                'total_product_tax_amount'            => formatDecimal($purchases->sum('product_tax_amount')),
                'total_recoverable_tax_amount'        => formatDecimal($purchases->sum('recoverable_tax_amount')),
                'total_recoverable_tax_calculated_on' => formatDecimal($purchases->sum('recoverable_tax_calculated_on')),
            ],
        ];

        return [
            'title'      => 'General Report',
            'heading'    => 'Please review the report below',
            'start_date' => $v['start_date'],
            'end_date'   => $v['end_date'],
            'data'       => $report,
        ];
    }

    public function index(Request $request)
    {
        if (method_exists($this, $request->report)) {
            if ($request->start_date && $request->end_date) {
                $diff = Carbon::parse($request->start_date)->diffInDays($request->end_date);
                if ($diff >= 93) {
                    return response(['message' => 'You only can get report for max 92 days, please reduce ' . ($diff - 92) . ' ' . str_plural('day', $diff - 92) . '.'], 422);
                }
            } elseif ($request->end_date) {
                $request->merge(['start_date' => Carbon::parse($request->end_date)->subDays(30)->toDateString()]);
            } elseif ($request->start_date) {
                $request->merge(['end_date' => Carbon::parse($request->start_date)->addDays(30)->toDateString()]);
            } else {
                $request->merge(['end_date' => Carbon::now()->toDateString()]);
                $request->merge(['start_date' => Carbon::parse($request->end_date)->subDays(30)->toDateString()]);
            }
            return $this->{$request->report}($request);
        } else {
            return response(['message' => 'The requested report is not found.'], 422);
        }
    }

    public function tax($request)
    {
        $v = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
        ]);

        $v['start_date'] = $v['start_date'] . ' 00:00:00';
        $v['end_date']   = $v['end_date'] . ' 23:59:59';

        $invoices             = \App\Invoice::with('taxes', 'items', 'items.taxes')->active()->whereBetween('date', [$v['start_date'], $v['end_date']])->get();
        $invoice_taxes        = [];
        $invoice_taxes_totals = [];
        foreach ($invoices as $invoice) {
            if (!empty($invoice->taxes) && $invoice->taxes->isNotEmpty()) {
                $invoice_taxes[] = $invoice->taxes;
            }
            foreach ($invoice->items as $item) {
                if (!empty($item->taxes) && $item->taxes->isNotEmpty()) {
                    $invoice_taxes[] = $item->taxes;
                }
            }
        }
        $invoice_taxes = collect($invoice_taxes)->flatten(1)->groupBy('id');
        foreach ($invoice_taxes as $id => $taxes) {
            $total_tax = 0;
            foreach ($taxes as $tax) {
                $total_tax += $tax->pivot->total_amount;
            }
            $invoice_taxes_totals[$id] = [
                'code'        => $tax->code,
                'name'        => $tax->name,
                'recoverable' => $tax->recoverable,
                'total'       => $total_tax,
            ];
        }

        $purchases             = \App\Purchase::with('taxes', 'items', 'items.taxes')->active()->whereBetween('date', [$v['start_date'], $v['end_date']])->get();
        $purchase_taxes        = [];
        $purchase_taxes_totals = [];
        foreach ($purchases as $purchase) {
            if (!empty($purchase->taxes) && $purchase->taxes->isNotEmpty()) {
                $purchase_taxes[] = $purchase->taxes;
            }
            foreach ($purchase->items as $item) {
                if (!empty($item->taxes) && $item->taxes->isNotEmpty()) {
                    $purchase_taxes[] = $item->taxes;
                }
            }
        }
        $purchase_taxes = collect($purchase_taxes)->flatten(1)->groupBy('id');
        foreach ($purchase_taxes as $id => $taxes) {
            $total_tax = 0;
            foreach ($taxes as $tax) {
                $total_tax += $tax->pivot->total_amount;
            }
            $purchase_taxes_totals[$id] = [
                'code'        => $tax->code,
                'name'        => $tax->name,
                'recoverable' => $tax->recoverable,
                'total'       => $total_tax,
            ];
        }

        $report = [
            'invoices' => [
                'taxes'            => $invoice_taxes_totals,
                'total'            => formatDecimal($invoices->sum('grand_total')),
                'total_tax_amount' => formatDecimal($invoices->sum('total_tax_amount')),
                // 'total_order_tax_amount' => formatDecimal($invoices->sum('order_tax_amount')),
                // 'total_product_tax_amount' => formatDecimal($invoices->sum('product_tax_amount')),
                'total_recoverable_tax_amount'        => formatDecimal($invoices->sum('recoverable_tax_amount')),
                'total_recoverable_tax_calculated_on' => formatDecimal($invoices->sum('recoverable_tax_calculated_on')),
            ],
            'purchases' => [
                'taxes'            => $purchase_taxes_totals,
                'total'            => formatDecimal($purchases->sum('grand_total')),
                'total_tax_amount' => formatDecimal($purchases->sum('total_tax_amount')),
                // 'total_order_tax_amount' => formatDecimal($purchases->sum('order_tax_amount')),
                // 'total_product_tax_amount' => formatDecimal($purchases->sum('product_tax_amount')),
                'total_recoverable_tax_amount'        => formatDecimal($purchases->sum('recoverable_tax_amount')),
                'total_recoverable_tax_calculated_on' => formatDecimal($purchases->sum('recoverable_tax_calculated_on')),
            ],
        ];

        return ['title' => 'Tax Report', 'heading' => 'Please review the tax report below', 'start_date' => $v['start_date'], 'end_date' => $v['end_date'], 'data' => $report];
    }

    public function user($request)
    {
        $v = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
            'user'       => 'required|exists:users,id',
        ]);

        $v['start_date'] = $v['start_date'] . ' 00:00:00';
        $v['end_date']   = $v['end_date'] . ' 23:59:59';
        $user            = \App\User::find($v['user']);
        $incomes         = $user->incomes()->whereBetween('created_at', [$v['start_date'], $v['end_date']])->get(['amount']);
        $expenses        = $user->expenses()->whereBetween('created_at', [$v['start_date'], $v['end_date']])->get(['amount']);
        $invoices        = $user->invoices()->active()->whereBetween('date', [$v['start_date'], $v['end_date']])->get(['grand_total']);
        $purchases       = $user->purchases()->active()->whereBetween('date', [$v['start_date'], $v['end_date']])->get(['grand_total']);

        $report = [
            'user'                  => $user,
            'total_income_amount'   => formatDecimal($incomes->sum('amount')),
            'total_expense_amount'  => formatDecimal($expenses->sum('amount')),
            'toral_invoice_amount'  => formatDecimal($invoices->sum('grand_total')),
            'total_purchase_amount' => formatDecimal($purchases->sum('grand_total')),
        ];

        return ['title' => 'User Report', 'heading' => 'Please review the user report below', 'start_date' => $v['start_date'], 'end_date' => $v['end_date'], 'data' => $report];
    }

    public function vendor($request)
    {
        $v = $request->validate([
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date'   => 'nullable|date_format:Y-m-d',
            'vendor'     => 'required|exists:vendors,id',
        ]);

        $v['start_date'] = $v['start_date'] . ' 00:00:00';
        $v['end_date']   = $v['end_date'] . ' 23:59:59';
        $vendor          = \App\Vendor::find($v['vendor']);
        $purchases       = $vendor->purchases()->active()->whereBetween('date', [$v['start_date'], $v['end_date']])->get(['grand_total']);

        $report = [
            'vendor'                => $vendor,
            'balance'               => $vendor->journal->getBalanceInDollars(),
            'total_purchase_amount' => formatDecimal($purchases->sum('grand_total')),
        ];

        return ['title' => 'Vendor Report', 'heading' => 'Please review the vendor report below', 'start_date' => $v['start_date'], 'end_date' => $v['end_date'], 'data' => $report];
    }
}
