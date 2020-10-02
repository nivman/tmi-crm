@extends('mail.template') 
@section('content')
<table class="main">
    <tr>
        <td class="wrapper">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Hi {{$customer_name}},</p>
                        <p>A new invoice has been created. Please click the button below to view your invoice.</p>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a href="{{$invoice_url}}" target="_blank">View Invoice</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>Thank you & we would love to do business with you again.</p>
                        <p>Regards!<br>{{config('app.name')}}</p>
                        <p></p>
                        <hr>
                        <p><small>If you’re having trouble clicking the "View Invoice" button, copy and paste the URL below into your web browser: {{$invoice_url}}</small></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
