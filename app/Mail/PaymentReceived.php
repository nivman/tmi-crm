<?php

namespace App\Mail;

use App\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentReceived extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        $customer = $this->payment->payable instanceof \App\Customer;
        return $this->subject($customer ? 'Payment Received!' : 'Payment Sent!')
            ->view('mail.payment.received')
            ->with([
                'customer'               => $customer,
                'gateway'                => $this->payment->gateway,
                'company'                => $this->payment->company,
                'payment_number'         => $this->payment->id,
                'payable_name'           => $this->payment->payable->name,
                'amount'                 => formatNumber($this->payment->amount),
                'payable_company'        => $this->payment->payable->company,
                'payment_url'            => URL::signedRoute('order', ['act' => 'payment', 'hash' => $this->payment->hash]),
                'gateway_transaction_id' => $this->payment->gateway_transaction_id,
            ]);
    }
}
