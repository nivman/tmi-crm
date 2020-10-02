<?php

namespace App\Http\Controllers;

use App\Income;
use App\Expense;
use App\Invoice;
use App\Payment;
use App\Purchase;
use Carbon\Carbon;
use App\Charts\BarChart;
use App\Charts\PieChart;
use App\Charts\LineChart;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function barChart(Request $request)
    {
        $year = $request->year ?: date('Y');
        $date = Carbon::create($year);

        $invoices  = chartData('App\Invoice', 'Y-m', 'm', 'toDays', $year);
        $purchases = chartData('App\Purchase', 'Y-m', 'm', 'toDays', $year);

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create($year, ($i < 10 ? '0' . $i : $i))->format('M y');
        }

        $config = [
            'title'    => $date->format('Y') . ' Overview',
            'datasets' => [
                'Invoices'  => $invoices,
                'Purchases' => $purchases,
            ],
            'labels'  => $labels,
            'options' => ['responsive' => false, 'maintainAspectRatio' => false, 'legend' => ['display' => true, 'position' => 'bottom']],
        ];
        $chart = new BarChart($config);

        return $chart->get();
    }

    public function customer()
    {
        $received_payment = Payment::myPayments()->received()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $due_payment = Payment::myPayments()->due()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $invoice = Invoice::myInvoices()->active()->groupBy('customer_id')
            ->selectRaw('sum(grand_total) as total_amount, sum(total_tax_amount) as total_tax_amount')->get()
            ->makeHidden(['taxes', 'customer'])->first();
        return ['invoice' => $invoice, 'payment' => ['received' => $received_payment, 'due' => $due_payment]];
    }

    public function lineChart(Request $request)
    {
        $year  = $request->year ?: date('Y');
        $month = $request->month ?: date('m');
        $date  = Carbon::create($year, $month);

        $invoices  = chartData('App\Invoice', 'Y-m-d', 'd', 'toDays', $year, $month);
        $purchases = chartData('App\Purchase', 'Y-m-d', 'd', 'toDays', $year, $month);

        for ($i = 1; $i <= $date->endOfMonth()->day; $i++) {
            $labels[] = Carbon::create($year, $month, $i)->format('jS');
        }

        $config = [
            'title'    => $date->format('F Y') . ' Overview',
            'datasets' => [
                'Invoices'  => $invoices,
                'Purchases' => $purchases,
            ],
            'labels'  => $labels,
            'options' => ['responsive' => false, 'maintainAspectRatio' => false, 'legend' => ['display' => true, 'position' => 'top']],
        ];
        $chart = new LineChart($config);

        return $chart->get();
    }

    public function pieChart(Request $request)
    {
        $year  = $request->year ?: date('Y');
        $month = $request->month ?: date('m');
        $date  = Carbon::create($year, $month);

        $config = [
            'title'    => $date->format('F Y') . ' Creations',
            'datasets' => [
                Invoice::mine()->monthly($year, $month)->count(),
                Purchase::mine()->monthly($year, $month)->count(),
                Income::mine()->monthly($year, $month)->count(),
                Expense::mine()->monthly($year, $month)->count(),
            ],
            'labels'  => ['Invoices', 'Purchases', 'Incomes', 'Expenses'],
            'options' => ['responsive' => false, 'maintainAspectRatio' => true, 'legend' => ['display' => true, 'position' => 'top']],
        ];
        $chart = new PieChart($config);

        return $chart->get();
    }

    public function vendor()
    {
        $received_payment = Payment::myPayments()->received()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $due_payment = Payment::myPayments()->due()->groupBy('payable_id')
            ->selectRaw('sum(amount) as total_amount')->get()
            ->makeHidden(['account', 'payable', 'user'])->first();
        $purchase = Purchase::myPurchases()->active()->groupBy('vendor_id')
            ->selectRaw('sum(grand_total) as total_amount, sum(total_tax_amount) as total_tax_amount')->get()
            ->makeHidden(['taxes', 'vendor'])->first();
        return ['purchase' => $purchase, 'payment' => ['received' => $received_payment, 'due' => $due_payment]];
    }
}
