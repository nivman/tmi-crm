<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@php $card_gateway = env('CARD_GATEWAY');
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payment {{ $payment->received ? 'Note' : 'Request' }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/minimal.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="order">
            <div class="no-print">
                @if (session('message'))
                <div class="stripe is-success">
                    <div class="message-body">
                        {{ session('message') }}
                    </div>
                </div>
                @endif @if (session('error'))
                <div class="stripe is-danger">
                    <div class="message-body">
                        {{ session('error') }}
                    </div>
                </div>
                @endif @if (session('info'))
                <div class="stripe">
                    <div class="message-body">
                        {{ session('info') }}
                    </div>
                </div>
                @endif @if ($request->gateway == 'paypal') @if ($request->type == 'cancel')
                <div class="stripe is-warning">
                    <div class="message-body">
                        We are sorry as payment has been failed, please try again.
                    </div>
                </div>
                @endif @endif
            </div>

            <table width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="top" width="55%">
                        <span>
                            <i><small>{{ $customer ? 'Customer' : 'Vendor' }} Details</small></i><br>
                            <big>
                                <strong>{{ $payment->payable->name }}</strong><br>
                                {{ $payment->payable->company }}
                            </big><br>
                            {{ $payment->payable->address }}<br>
                            {{ $payment->payable->state_name }}, {{ $payment->payable->country_name }}<br>
                            Phone: {{ $payment->payable->phone }}<br>
                            Email: {{ $payment->payable->email }}<br>
                            @foreach($payment->payable->attributes as $attr)
                            @if($payment->payable->{$attr->slug})
                            {{ $attr->name }}: {{ $payment->payable->{$attr->slug} }}<br>
                            @endif @endforeach
                        </span>
                    </td>
                    <td align="right">
                        <span>
                            <strong><big>{{ $payment->company->name }}</big></strong><br>
                            {{ $payment->company->address }}<br>
                            {{ $payment->company->state_name }}, {{ $payment->company->country_name }}<br>
                            Phone: {{ $payment->company->phone }}<br>
                            Email: {{ $payment->company->email }}<br>
                            @for($i = 0; $i < 5 ; $i++) @if(isset($payment->company->extra['cf'.$i.'_label']) && isset($payment->company->extra['cf'.$i.'_value']) && !empty($payment->company->extra['cf'.$i.'_label'])
                            && !empty($payment->company->extra['cf'.$i.'_value']))
                                {{ $payment->company->extra['cf'.$i.'_label'] }}: {{ $payment->company->extra['cf'.$i.'_value'] }}<br>
                            @endif @endfor
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>
            <hr>
            <table width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left">
                        <strong><big>Payment {{ $payment->received ? 'Note' : 'Request' }}</big></strong>
                    </td>
                    <td align="right" valign="top">
                        <span>
                            <strong>Payment No: {{ $payment->id }}</strong><br>
                        </span>
                        <span>
                            Date: {{ \Carbon\Carbon::parse($payment->created_at)->format(php_date_formate()) }}<br>
                        </span>
                        <span>
                            Reference: {{ $payment->reference }}<br>
                        </span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>

            <table width="650" class="borderless" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table width="100%" class="borderless" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <big><strong>Amount {{ $payment->received ? ($customer ? ' Received' : ' Sent') : ''}}</strong>
                                            </big>
                                    </td>
                                    <td align="right">
                                        <big><strong>{{ formatNumber($payment->amount) }}</strong></big>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <p>{{ $payment->note }}</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="center">
                        <br>This is a computer-generated document. No signature is required.
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                </tr>
            </table>

            <div class="no-print">
                @if (!$payment->received)
                <hr>
                <table width="650" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    @if ($card_gateway == 'Stripe')
                    <tr>
                        <td colspan="3">
                            <div class="stripe">
                                <form action="/pay/stripe/{{$payment->hash}}" method="post" id="payment-form">
                                    @csrf Pay with credit or debit card<br><br>
                                    <div class="form-row">
                                        <div id="card-element"></div>
                                        <div id="card-errors" role="alert"></div>
                                    </div>
                                    <div class="payment-buttons">
                                        <button type="submit" id="stripe-btn" class="button is-link is-fullwidth">Submit Payment</button>
                                    </div>
                                    @if (env('PAYPAL_ENABLED'))
                                    <span class="or">OR</span>
                                    <a class="paypal" href="/paypal/{{$payment->hash}}">Pay With <img src="/images/paypal-logo.svg" alt="PayPal" class="m-l-sm"></a>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @elseif($card_gateway == 'PayPal_Rest' || $card_gateway == 'AuthorizeNetApi_Api' || $card_gateway == 'PayPal_Pro')
                    <tr>
                        <td colspan="3" align="center">
                            <big>Pay with credit or debit card ({{ $card_gateway == 'PayPal_Rest' ? 'PayPal Rest' : ($card_gateway == 'PayPal_Pro' ? 'PayPal
                        Pro' : '')}})</big>
                        </td>
                    </tr>
                    <tr>
                        <td width="25%"></td>
                        <td width="50%" align="left">
                            <div class="stripe">
                                <form action="/pay/{{$card_gateway}}/{{$payment->hash}}" method="post" autocomplete="off" class="card-form">
                                    @csrf
                                    <div class="field">
                                        <label for="firstName">First Name</label>
                                        <input type="text" name="firstName" value="" id="firstName" class="input" required="required">
                                    </div>
                                    <div class="field">
                                        <label for="lastName">Last Name</label>
                                        <input type="text" name="lastName" value="" id="lastName" class="input" required="required">
                                    </div>
                                    <div class="field">
                                        <label for="billingAddress1">Billing Address</label>
                                        <input type="text" name="billingAddress1" value="{{$payment->payable->address}}" id="billingAddress1" class="input" required="required">
                                    </div>
                                    <div class="field">
                                        <label>Billing City</label>
                                        <input type="text" name="billingCity" value="" class="input" required="required">
                                    </div>
                                    <div class="field">
                                        <label>ZIP/Postal Code</label>
                                        <input type="text" name="billingPostcode" value="" class="input" required="required">
                                    </div>
                                    <div class="field">
                                        <label>Billing State</label>
                                        <input type="hidden" name="billingState" value="{{$payment->payable->state}}">
                                        <input type="text" name="billingStateName" value="{{$payment->payable->state_name}}" class="input" required="required" readonly
                                            disabled>
                                    </div>
                                    <div class="field">
                                        <label>Billing Country</label>
                                        <input type="hidden" name="billingCountry" value="{{$payment->payable->country}}">
                                        <input type="text" name="billingCountryName" value="{{$payment->payable->country_name}}" class="input" required="required"
                                            readonly disabled>
                                    </div>
                                    <div class="field">
                                        <label for="number">Card Number</label>
                                        <input type="text" name="number" value="" id="number" class="input" required="required" autocomplete="nope">
                                    </div>
                                    <div class="field">
                                        <label for="expiryMonth">Expiry Month</label>
                                        <select name="expiryMonth" required="required" id="expiryMonth">
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ date('m', mktime(0, 0, 0, $m, 1)) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                                    </div>
                                    <div class="field">
                                        <label for="expiryYear">Expiry Year</label>
                                        <select name="expiryYear" required="required" id="expiryYear">
                                    @for($y=date('Y'); $y<=(date('Y')+10); $y++)
                                        <option value="{{$y}}">{{ $y }}</option>
                                    @endfor
                                </select>
                                    </div>
                                    <div class="field">
                                        <label for="cvv">CVV</label>
                                        <input type="text" name="cvv" minlength="3" maxlength="4" value="" id="cvv" class="input" required="required">
                                    </div>
                                    <div class="payment-buttons">
                                        <button type="submit" class="button is-link is-fullwidth">Submit Payment</button>
                                    </div>
                                    @if (env('PAYPAL_ENABLED'))
                                    <span class="or">OR</span>
                                    <a class="paypal" href="/paypal/{{$payment->hash}}">Pay With <img src="/images/paypal-logo.svg" alt="PayPal" style="margin-left:5px;"></a>
                                    @endif
                                </form>
                            </div>
                        </td>
                        <td width="25%"></td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    @endif

                    <tr class="stripe">
                        <td colspan="3">
                            @if($accounts->isNotEmpty())
                            <table width="100%">
                                <tr>
                                    <td colspan="2">
                                        <big>Offline Payment Options</big>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        For your convenience, you may make payment of the final amount to one of the following:
                                    </td>
                                </tr>
                                @foreach ($accounts as $account)
                                <tr>
                                    <td colspan="2">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <big>{{ $account->name }}</big><br>
                                        <strong><big>{{ $account->reference }}</big></strong><br> {{ $account->details }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            @endif
                        </td>
                    </tr>
                </table>
                @endif
            </div>
        </div>
    </div>

    @if (!$payment->received && $card_gateway == 'Stripe')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('{{env('STRIPE_KEY')}}');
            var elements = stripe.elements();

            var style = {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                    color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            };

            var card = elements.create('card', {style: style});
            card.mount('#card-element');

            card.addEventListener('change', function(event) {
                var displayError = document.getElementById('card-errors');
                if (event.error) {
                    displayError.textContent = event.error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                document.querySelector('#stripe-btn').disabled = true;
            document.querySelector('#stripe-btn').classList.add('is-loading');
                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        var errorElement = document.getElementById('card-errors');
                        errorElement.textContent = result.error.message;
                        document.querySelector('#stripe-btn').disabled = false;
                        document.querySelector('#stripe-btn').classList.remove('is-loading');
                    } else {
                        var hiddenInput = document.createElement('input');
                        hiddenInput.setAttribute('type', 'hidden');
                        hiddenInput.setAttribute('name', 'stripeToken');
                        hiddenInput.setAttribute('value', result.token.id);
                        form.appendChild(hiddenInput);
                        form.submit();
                    }
                });
            });
    </script>
    @endif
</body>

</html>
