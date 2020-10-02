<?php

namespace App\Providers;

use Install\Helpers\Install;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Rinvex\Attributes\Models\Attribute;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Install::routes();
        // Schema::defaultStringLength(191);
        \App\Income::observe(\App\Observers\IncomeObserver::class);
        \App\Expense::observe(\App\Observers\ExpenseObserver::class);
        \App\Invoice::observe(\App\Observers\InvoiceObserver::class);
        \App\Payment::observe(\App\Observers\PaymentObserver::class);
        \App\Purchase::observe(\App\Observers\PurchaseObserver::class);
        \App\Transfer::observe(\App\Observers\TransferObserver::class);
        \App\InvoiceItem::observe(\App\Observers\InvoiceItemObserver::class);
        \App\PurchaseItem::observe(\App\Observers\PurchaseItemObserver::class);

        Attribute::typeMap([
            'text'     => \Rinvex\Attributes\Models\Type\Text::class,
            'boolean'  => \Rinvex\Attributes\Models\Type\Boolean::class,
            'integer'  => \Rinvex\Attributes\Models\Type\Integer::class,
            'varchar'  => \Rinvex\Attributes\Models\Type\Varchar::class,
            'datetime' => \Rinvex\Attributes\Models\Type\Datetime::class,
        ]);

        Collection::macro('toDays', function () {
            $data = $this->toArray();
            $days = [];
            for ($i = 1; $i <= 31; $i++) {
                $days[$i] = 0;
            }
            // $result = array_map('floatval', $this->intersect($days));
            $result = array_map('floatval', array_intersect_key($data + $days, $days));
            ksort($result);
            return collect($result);
        });

        Collection::macro('toMonths', function () {
            $data = $this->toArray();
            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[$i] = 0;
            }
            // $result = array_map('floatval', $this->intersect($months));
            $result = array_map('floatval', array_intersect_key($data + $months, $months));
            ksort($result);
            return collect($result);
        });
    }

    public function register()
    {
        //
    }
}
