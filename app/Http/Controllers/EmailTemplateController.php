<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function show($template = 'user_created')
    {

        $view = $this->{camel_case($template)}();
        return response()->json([
            'footer'   => str_after($view, '<!-- Template End -->'),
            'header'   => str_before($view, '<!-- Template Start -->'),
            'template' => trim(str_before(str_after($view, '<!-- Template Start -->'), '<!-- Template End -->')),
        ]);
    }

    public function update(Request $request, $template = 'user_created')
    {

        $path = resource_path('views/' . $this->{camel_case($template)}(true) . '.blade.php');
        $data = "@extends('mail.template')\n@section('content')\n" . $request->template . "\n@endsection\n";
        return response()->json(['success' => file_put_contents($path, $data), 'template' => $request->template]);
    }

    private function invoiceCreated($view = false)
    {
        if ($view) {
            return 'mail/invoice/created';
        }
        $data = [
            'company'          => \App\Company::first(),
            'grand_total'      => '{{$grand_total}}',
            'invoice_url'      => '{{$invoice_url}}',
            'customer_name'    => '{{$customer_name}}',
            'invoice_number'   => '{{$invoice_number}}',
            'customer_company' => '{{$customer_company}}',
        ];
        return view('mail.invoice.created', $data);
    }

    private function paymentCreated($view = false)
    {
        if ($view) {
            return 'mail/payment/created';
        }
        $data = [
            'amount'          => '{{$amount}}',
            'customer'        => '{{$customer}}',
            'company'         => \App\Company::first(),
            'payment_url'     => '{{$payment_url}}',
            'payable_name'    => '{{$payable_name}}',
            'payment_number'  => '{{$payment_number}}',
            'payable_company' => '{{$payable_company}}',
        ];
        return view('mail.payment.created', $data);
    }

    private function paymentReceived($view = false)
    {
        if ($view) {
            return 'mail/payment/received';
        }
        $data = [
            'amount'                 => '{{$amount}}',
            'gateway'                => '{{$gateway}}',
            'customer'               => '{{$customer}}',
            'company'                => \App\Company::first(),
            'payment_url'            => '{{$payment_url}}',
            'payable_name'           => '{{$payable_name}}',
            'payment_number'         => '{{$payment_number}}',
            'payable_company'        => '{{$payable_company}}',
            'gateway_transaction_id' => '{{$gateway_transaction_id}}',
        ];
        return view('mail.payment.received', $data);
    }

    private function purchaseCreated($view = false)
    {
        if ($view) {
            return 'mail/purchase/created';
        }
        $data = [
            'company'         => \App\Company::first(),
            'grand_total'     => '{{$grand_total}}',
            'vendor_name'     => '{{$vendor_name}}',
            'purchase_url'    => '{{$purchase_url}}',
            'vendor_company'  => '{{$vendor_company}}',
            'purchase_number' => '{{$purchase_number}}',
        ];
        return view('mail.purchase.created', $data);
    }

    private function userCreated($view = false)
    {
        if ($view) {
            return 'mail/user/created';
        }
        $data = [
            'name'      => '{{ $name }}',
            'email'     => '{{ $email }}',
            'username'  => '{{ $username }}',
            'password'  => '{{ $password }}',
            'login_url' => '{{ $login_url }}',
            'company'   => \App\Company::first(),
        ];
        return view('mail.user.created', $data);
    }

    private function userReset($view = false)
    {
        if ($view) {
            return 'mail/user/reset';
        }
        $data = [
            'name'      => '{{ $name }}',
            'email'     => '{{ $email }}',
            'username'  => '{{ $username }}',
            'password'  => '{{ $password }}',
            'login_url' => '{{ $login_url }}',
            'company'   => \App\Company::first(),
        ];
        return view('mail.user.reset', $data);
    }
}
