<?php

namespace App\Console\Commands;

use Ulid\Ulid;
use Carbon\Carbon;
use App\Helpers\Order;
use Illuminate\Console\Command;

class CreateRecurringInvoices extends Command
{
    protected $description = 'Create recurring invoices';
    protected $signature   = 'recurring:create';

    public function __construct()
    {
        parent::__construct();
    }

    protected function biennially($date)
    {
        return $date->startOfDay()->addYears(2)->lte(Carbon::now()->startOfDay());
    }

    protected function create($recurring)
    {
        $recurring->products = $recurring->items->toArray();
        $v                   = Order::prepareData($recurring->toArray(), $recurring);
        $v['draft']          = false;
        $v['date']           = Carbon::now();
        $v['recurring_id']   = $recurring->id;
        $v['reference']      = Ulid::generate(true);
        $invoice             = Order::orderTransaction($v, 'App\Invoice');
        $invoice->customer->payments()->create([
            'received'   => false,
            'invoice_id' => $invoice->id,
            'reference'  => Ulid::generate(true),
            'amount'     => $invoice->grand_total,
            'account_id' => env('DEFAULT_ACCOUNT'),
            'note'       => 'This is auto generated payment request for the invoice number ' . $invoice->id,
        ]);
    }

    protected function daily($date)
    {
        return $date->startOfDay()->addDay()->lte(Carbon::now()->startOfDay());
    }

    protected function monthly($date)
    {
        return $date->startOfDay()->addMonth()->lte(Carbon::now()->startOfDay());
    }

    protected function quarterly($date)
    {
        return $date->startOfDay()->addQuarter()->lte(Carbon::now()->startOfDay());
    }

    protected function semiannual($date)
    {
        return $date->startOfDay()->addMonths(6)->lte(Carbon::now()->startOfDay());
    }

    protected function weekly($date)
    {
        return $date->startOfDay()->addWeek()->lte(Carbon::now()->startOfDay());
    }

    protected function yearly($date)
    {
        return $date->startOfDay()->addYear()->lte(Carbon::now()->startOfDay());
    }

    public function handle()
    {
        $recurrings = \App\Recurring::active()->get()->filter(function ($recurring) {
            $date = $recurring->last_created_at ? $recurring->last_created_at : $recurring->start_date;
            if ($recurring->create_before) {
                $date = $recurring->create_before == 1 ? $date->subDay() : $date->subDays($recurring->create_before);
            }
            return $this->{$recurring->repeat}($date);
        });

        $recurrings->each(function ($recurring) {
            $this->create($recurring);
            $recurring->disableLogging();
            $recurring->refresh()->update(['last_created_at' => date('Y-m-d')]);
        });

        activity()->withProperties($recurrings)
            ->log($recurrings->count() . ' invoices have been created with recurring:create command.');
        $this->info(sprintf('%d invoices have been created with recurring:create command.', $recurrings->count()));
    }
}
