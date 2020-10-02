@extends('mail.template')
@section('content')
<table class="main">
    <tr>
        <td class="wrapper">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Hi {{$payable_name}},</p>
                        <p>A new payment request has been created. Please click the button below to view request and/or to make
                            payment.
                        </p>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a href="{{$payment_url}}" target="_blank">View Payment</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>Thank you for doing business with us and we would appreciate if you could make payment at your earliest
                            convenience.
                        </p>
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
