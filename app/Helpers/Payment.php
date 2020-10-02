<?php

namespace App\Helpers;

class Payment
{
    public $gateway;

    public function __construct($gateway, $testMode = false)
    {
        $this->gateway = \Omnipay\Omnipay::create($gateway);
        if ($gateway == 'Stripe') {
            $this->gateway->setApiKey(env('STRIPE_SECRET'));
        } elseif ($gateway == 'PayPal_Pro') {
            $this->gateway->setUsername(env('PAYPAL_USERNAME'));
            $this->gateway->setPassword(env('PAYPAL_PASSWORD'));
            $this->gateway->setSignature(env('PAYPAL_SIGNATURE'));
        } elseif ($gateway == 'AuthorizeNetApi_Api') {
            $this->gateway->setAuthName(env('AUTHORIZE_LOGIN'));
            $this->gateway->setTransactionKey(env('AUTHORIZE_TRANSACTION_KEY'));
        } elseif ($gateway == 'PayPal_Rest') {
            $this->gateway->setSecret(env('PAYPAL_SECRET'));
            $this->gateway->setClientId(env('PAYPAL_CLIENT_ID'));
        } else {
            abort(500, 'Unknown payment gateway!');
        }
        $this->gateway->setTestMode($testMode);
    }

    public function purchase($data)
    {
        return $this->gateway->purchase($data)->send();
    }
}
