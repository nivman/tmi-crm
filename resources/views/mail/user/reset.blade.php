@extends('mail.template') 
@section('content')
<table class="main">
    <tr>
        <td class="wrapper">
            <table border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <p>Hi {{$name}},</p>
                        <p>Your account password has been reset as following:</p>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                            <tbody>
                                <tr>
                                    <td width="100px">Username</td>
                                    <td>{{$username}}</td>
                                </tr>
                                <tr>
                                    <td width="100px">Password</td>
                                    <td>{{$password}}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                            <tbody>
                                <tr>
                                    <td align="center">
                                        <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <a href="{{$login_url}}" target="_blank">Login</a>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <p>Thank you!</p>
                        <p>Regards!<br>{{config('app.name')}}</p>
                        <p></p>
                        <hr>
                        <p><small>If you’re having trouble clicking the "Login" button, copy and paste the URL below into your web browser: {{$login_url}}</small></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
