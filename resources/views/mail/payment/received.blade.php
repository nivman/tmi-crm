@extends('mail.template')
@section('content')
<table class="main">
    <tr>
        <td class="wrapper">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Hi {{$payable_name}},</p>
                        <p>This is to confirm that we have {{ $customer ? 'received' : 'sent' }} a payment of {{ $amount }}
                            {{ $customer ? ' by '.($gateway.($gateway_transaction_id ? " (Transaction Id: {$gateway_transaction_id})" : '')).'.' : '' }}
                            You can view the payment note by clicking the button below:
                        </p>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a href="{{$payment_url}}" target="_blank">View Payment Note</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>Thank you very much.</p>
                        <p>Regards!<br>{{config('app.name')}}</p>
                        <p></p>
                        <hr>
                        <p><small>If you’re having trouble clicking the "View Payment" button, copy and paste the URL below into your web browser: {{$payment_url}}</small></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
