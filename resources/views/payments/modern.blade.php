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
    <title>Payment Number {{ $payment->id }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/modern.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="wrapper">
            <div class="order">
                <div class="order-header">
                    @if (session('message'))
                    <div class="message is-success">
                        <div class="message-body">
                            {{ session('message') }}
                        </div>
                    </div>
                    @endif @if (session('error'))
                    <div class="message is-danger">
                        <div class="message-body">
                            {{ session('error') }}
                        </div>
                    </div>
                    @endif @if (session('info'))
                    <div class="message">
                        <div class="message-body">
                            {{ session('info') }}
                        </div>
                    </div>
                    @endif @if ($request->gateway == 'paypal') @if ($request->type == 'cancel')
                    <div class="message is-warning">
                        <div class="message-body">
                            We are sorry as payment has been failed, please try again.
                        </div>
                    </div>
                    @endif @endif
                    <img alt="{{ $payment->company->name }}" class="logo screen" src="/images/default.png" />
                    <div class="vcard company-address">
                        <div class="fn org"><strong>{{ $payment->company->name }}</strong>
                        </div>
                        <div class="adr">
                            <div class="street-address">
                                {{ $payment->company->address }}<br> {{ $payment->company->state_name }}, {{ $payment->company->country_name
                                }} <br>
                            </div>
                            <div>Phone: {{ $payment->company->phone }}</div>
                            <div>Email: {{ $payment->company->email }}</div>
                            @for($i = 0; $i
                            < 5 ; $i++)@if(isset($payment->company->extra['cf'.$i.'_label']) && isset($payment->company->extra['cf'.$i.'_value']) && !empty($payment->company->extra['cf'.$i.'_label'])
                                && !empty($payment->company->extra['cf'.$i.'_value']))
                                <div>{{ $payment->company->extra['cf'.$i.'_label'] }}: {{ $payment->company->extra['cf'.$i.'_value']
                                    }}
                                </div>
                                @endif @endfor
                        </div>
                    </div>
                </div>

                <div class="order-info">
                    <h2>Payment No. <strong>{{ $payment->id }}</strong></h2>
                    <h3>Date: {{ \Carbon\Carbon::parse($payment->created_at)->format(php_date_formate()) }}</h3>
                    <h3>Reference: {{ $payment->reference }}</h3>
                </div>

                <div class="vcard client-details">
                    <div class="light">{{ $customer ? 'Customer' : 'Vendor' }} Details:</div>
                    <div class="fn">{{ $payment->payable->name }}</div>
                    <div class="org">{{ $payment->payable->company }}</div>
                    <div class="adr">
                        <div class="street-address">
                            {{ $payment->payable->address }}<br>{{ $payment->payable->state_name }}, {{ $payment->payable->country_name
                            }}
                            <br>
                        </div>
                        <div>Phone: {{ $payment->payable->phone }}</div>
                        <div>Email: {{ $payment->payable->email }}</div>
                        @foreach($payment->payable->attributes as $attr) @if ($payment->payable->{$attr->slug})
                        <div>{{ $attr->name }}: {{ $payment->payable->{$attr->slug} }}</div>
                        @endif @endforeach
                    </div>
                </div>

                <table>
                    <thead>
                        <tr class="header_row">
                            <th colspan="2">Payment {{ $payment->received ? 'Note' : 'Request' }} Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <h2 class="amount">Amount{{ $payment->received ? ($customer ? ' Received' : ' Sent') : ''}}:</h2>
                            </td>
                            <td>
                                <h2 class="amount" style="text-align:right">{{ formatNumber($payment->amount) }}</h2>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                {{ $payment->note }}
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="order-footer">
                    This is a computer-generated document. No signature is required.
                </div>
            </div>


            @if (!$payment->received) {{-- payment form --}}
            <div class="order order1 no-print">
                @if ($card_gateway == 'Stripe')
                <div class="stripe">
                    <form action="/pay/stripe/{{$payment->hash}}" method="post" id="order-form">
                        @csrf
                        <div class="form-row">
                            <div class="field">
                                <label class="label" for="card-element">Pay with credit or debit card</label>
                                <div class="control">
                                    <div id="card-element"></div>
                                </div>
                                <div id="card-errors" role="alert"></div>
                            </div>
                        </div>
                        <div class="order-buttons">
                            <button type="submit" id="stripe-btn" class="button is-link is-fullwidth">Submit Payment</button>
                        </div>
                        @if (env('PAYPAL_ENABLED'))
                        <span class="or">OR</span>
                        <a class="paypal" href="/paypal/{{$payment->hash}}">Pay With <img src="/images/paypal-logo.svg" alt="PayPal" class="m-l-sm"></a>
                        @endif
                    </form>
                </div>
                @elseif ($card_gateway == 'PayPal_Rest' || $card_gateway == 'AuthorizeNetApi_Api' || $card_gateway == 'PayPal_Pro')
                <div class="panel panel-border is-shadowless">
                    <p class="panel-heading">
                        Pay with credit or debit card ({{ $card_gateway == 'PayPal_Rest' ? 'PayPal Rest' : ($card_gateway == 'PayPal_Pro' ? 'PayPal
                        Pro' : '')}})
                    </p>
                    <div class="panel-block">
                        <form action="/pay/{{$card_gateway}}/{{$payment->hash}}" method="post" autocomplete="off" class="card-form">
                            @csrf
                            <div class="columns is-multiline p-t-md">
                                <div class="column is-6 p-t-none">
                                    <div class="field">
                                        <label class="label" for="firstName">First Name</label>
                                        <div class="control">
                                            <input type="text" name="firstName" value="" id="firstName" class="input" required="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6 p-t-none">
                                    <div class="field">
                                        <label class="label" for="lastName">Last Name</label>
                                        <div class="control">
                                            <input type="text" name="lastName" value="" id="lastName" class="input" required="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-12 p-t-none">
                                    <div class="field">
                                        <label class="label" for="billingAddress1">Billing Address</label>
                                        <div class="control">
                                            <input type="text" name="billingAddress1" value="{{$payment->payable->address}}" id="billingAddress1" class="input" required="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6 p-t-none">
                                    <div class="field">
                                        <label class="label">Billing City</label>
                                        <div class="control">
                                            <input type="text" name="billingCity" value="" class="input" required="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6 p-t-none">
                                    <div class="field">
                                        <label class="label">Billing ZIP/Postal Code</label>
                                        <div class="control">
                                            <input type="text" name="billingPostcode" value="" class="input" required="required">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6 p-t-none">
                                    <div class="field">
                                        <label class="label">Billing State</label>
                                        <div class="control">
                                            <input type="hidden" name="billingState" value="{{$payment->payable->state}}">
                                            <input type="text" name="billingStateName" value="{{$payment->payable->state_name}}" class="input" required="required" readonly
                                                disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-6 p-t-none">
                                    <div class="field">
                                        <label class="label">Billing Country</label>
                                        <div class="control">
                                            <input type="hidden" name="billingCountry" value="{{$payment->payable->country}}">
                                            <input type="text" name="billingCountryName" value="{{$payment->payable->country_name}}" class="input" required="required"
                                                readonly disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-12 p-t-none">
                                    <div class="field">
                                        <label class="label" for="number">Card Number</label>
                                        <div class="control">
                                            <input type="text" name="number" value="" id="number" class="input" required="required" autocomplete="nope">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-5 p-t-none">
                                    <div class="field">
                                        <label class="label" for="expiryMonth">Expiry Month</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select name="expiryMonth" required="required" id="expiryMonth">
                                                                @for($m=1; $m<=12; $m++)
                                                                    <option value="{{ date('m', mktime(0, 0, 0, $m, 1)) }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                                                @endfor
                                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-4 p-t-none">
                                    <div class="field">
                                        <label class="label" for="expiryYear">Expiry Year</label>
                                        <div class="control">
                                            <div class="select is-fullwidth">
                                                <select name="expiryYear" required="required" id="expiryYear">
                                                                @for($y=date('Y'); $y<=(date('Y')+10); $y++)
                                                                    <option value="{{$y}}">{{ $y }}</option>
                                                                @endfor
                                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-3 p-t-none">
                                    <div class="field">
                                        <label class="label" for="cvv">CVV</label>
                                        <div class="control">
                                            <input type="text" name="cvv" minlength="3" maxlength="4" value="" id="cvv" class="input" required="required">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="columns is-multiline m-b-sm">
                                <div class="column">
                                    <div class="order-buttons">
                                        <button type="submit" class="button is-link is-fullwidth">Submit Payment</button>
                                    </div>
                                    @if (env('PAYPAL_ENABLED'))
                                    <span class="or">OR</span>
                                    <a class="paypal" href="/paypal/{{$payment->hash}}">Pay With <img src="/images/paypal-logo.svg" alt="PayPal" class="m-l-sm"></a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @endif @if($accounts->isNotEmpty())
                <div class="panel panel-border is-shadowless" v-if="$payment->note">
                    <p class="panel-heading">Offline Payment Options</p>
                    <div class="panel-block">
                        <div class="columns is-multiline m-b-xxs">
                            <div class="column is-12">
                                <p>For your convenience, you may make payment of the final amount to one of the following</p>
                            </div>
                            @foreach ($accounts as $account)
                            <div class="column is-6">
                                <div class="message">
                                    <div class="message-header">
                                        <p>{{ $account->name }}</p>
                                    </div>
                                    <div class="message-body">
                                        <strong>{{ $account->reference }}</strong><br> {{ $account->details }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
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
