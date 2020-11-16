<?php

namespace App\Helpers;

class PayPal
{
    public function gateway()
    {
        $gateway = \Omnipay\Omnipay::create('PayPal_Express');

        $gateway->setUsername(env('PAYPAL_USERNAME'));
        $gateway->setPassword(env('PAYPAL_PASSWORD'));
        $gateway->setSignature(env('PAYPAL_SIGNATURE'));
        $gateway->setBrandName(env('APP_NAME'));
        // $gateway->setLogoImageUrl();
        // $gateway->setHeaderImageUrl();
        return $gateway;
    }

    public function purchase($data)
    {
        return $this->gateway()->purchase($data)->send();
    }

    public function complete($data)
    {
        return $this->gateway()->completePurchase($data)->send();
    }

    public function formatAmount($amount)
    {
        return number_format($amount, 2, '.', '');
    }

    public function getCancelUrl($hash)
    {
        return url('view/payment/' . $hash . '?gateway=paypal&type=cancel');
    }

    public function getReturnUrl($hash)
    {
        return url('paypal/' . $hash . '/completed');
    }

    public function getNotifyUrl()
    {
    }
}
