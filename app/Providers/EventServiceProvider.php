<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        'App\Events\PaymentEvent' => [
            'App\Listeners\SyncPaymentToInvoices',
            'App\Listeners\SyncPaymentToPurchases',
        ],
        'App\Events\InvoiceEvent' => [
            'App\Listeners\CheckPaymentsForInvoice',
        ],
        'App\Events\PurchaseEvent' => [
            'App\Listeners\CheckPaymentsForPurchase',
        ],
        // 'App\Events\InvoiceCreated' => [
        //     'App\Listeners\InvoiceCreated',
        // ],
        // 'App\Events\InvoiceUpdated' => [
        //     'App\Listeners\InvoiceUpdated',
        // ],
        // 'App\Events\InvoiceDeleted' => [
        //     'App\Listeners\InvoiceDeleted',
        // ],
        // 'App\Events\PurchaseCreated' => [
        //     'App\Listeners\PurchaseCreated',
        // ],
        // 'App\Events\PurchaseUpdated' => [
        //     'App\Listeners\PurchaseUpdated',
        // ],
        // 'App\Events\PurchaseDeleted' => [
        //     'App\Listeners\PurchaseDeleted',
        // ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
