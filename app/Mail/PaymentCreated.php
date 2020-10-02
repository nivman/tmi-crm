<?php

namespace App\Mail;

use App\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\URL;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject('Payment Request Created!')
            ->view('mail.payment.created')
            ->with([
                'company' => $this->payment->company,
                'payment_number' => $this->payment->id,
                'payable_name' => $this->payment->payable->name,
                'amount' => formatNumber($this->payment->amount),
                'payable_company' => $this->payment->payable->company,
                'payment_url' => URL::signedRoute('order', ['act' => 'payment', 'hash' => $this->payment->hash]),
                'customer' => $this->payment->payable instanceof \App\Customer,
            ]);
    }
}
