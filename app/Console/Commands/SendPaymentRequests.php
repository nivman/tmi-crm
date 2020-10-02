<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendPaymentRequests extends Command
{
    protected $description = 'Send request for due payments';
    protected $signature   = 'payment:request';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $payments = \App\Payment::due()->get();
        $payments->each(function ($payment) {
            try {
                \Mail::to($payment->payable->email)->send(new \App\Mail\PaymentCreated($payment));
            } catch (\Exception $e) {
                \Log::error('Unable to send email, please check your system settings. Error: ' . $e->getMessage());
            }
        });
        activity()->withProperties($payments)
            ->log($payments->count() . ' payment requests have been sent with payment:request command.');
        $this->info(sprintf('%d payment requests have been sent with payment:request command.', $payments->count()));
    }
}
