<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@php $col = 1; if ($invoice->company->show_discount) { $col++; } if ($invoice->company->show_tax) { $col++; }
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Number {{ $invoice->id }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/modern.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="wrapper">
            <div class="order">
                <div class="order-header">
                    <img alt="{{ $invoice->company->name }}" class="logo screen" src="/images/default.png" />
                    <div class="vcard company-address">
                        <div class="fn org"><strong>{{ $invoice->company->name }}</strong>
                        </div>
                        <div class="adr">
                            <div class="street-address">
                                {{ $invoice->company->address }}<br> {{ $invoice->company->state_name }}, {{ $invoice->company->country_name
                                }} <br>
                            </div>
                            <div>Phone: {{ $invoice->company->phone }}</div>
                            <div>Email: {{ $invoice->company->email }}</div>
                            @for($i = 0; $i
                            < 5 ; $i++) @if(isset($invoice->company->extra['cf'.$i.'_label']) && isset($invoice->company->extra['cf'.$i.'_value']) && !empty($invoice->company->extra['cf'.$i.'_label'])
                                && !empty($invoice->company->extra['cf'.$i.'_value']))
                                <div>{{ $invoice->company->extra['cf'.$i.'_label'] }}: {{ $invoice->company->extra['cf'.$i.'_value']
                                    }}
                                </div>
                                @endif @endfor
                        </div>
                    </div>
                </div>

                <div class="order-info">
                    <h2>Invoice No. <strong>{{ $invoice->id }}</strong></h2>
                    <h3>Date: {{ \Carbon\Carbon::parse($invoice->date)->format(php_date_formate()) }}</h3>
                    <h3>Reference: {{ $invoice->reference }}</h3>
                </div>

                <div class="vcard client-details">
                    <div class="light">Invoiced to:</div>
                    <div class="fn">{{ $invoice->customer->name }}</div>
                    <div class="org">{{ $invoice->customer->company }}</div>
                    <div class="adr">
                        <div class="street-address">
                            {{ $invoice->customer->address }}<br>{{ $invoice->customer->state_name }}, {{ $invoice->customer->country_name
                            }}
                            <br>
                        </div>
                        <div>Phone: {{ $invoice->customer->phone }}</div>
                        <div>Email: {{ $invoice->customer->email }}</div>
                        @foreach($invoice->customer->attributes as $attr) @if ($invoice->customer->{$attr->slug})
                        <div>{{ $attr->name }}: {{ $invoice->customer->{$attr->slug} }}</div>
                        @endif @endforeach
                    </div>
                </div>

                <table class="order-amount">
                    <thead>
                        <tr class="header_row">
                            <th>Item / Details</th>
                            <th class="has-text-centered">Price</th>
                            @if ($invoice->company->show_discount)
                            <th class="has-text-centered colfix">Discount</th>
                            @endif @if ($invoice->company->show_tax)
                            <th style="width:125px;" class="has-text-centered">Tax</th>
                            @endif
                            <th class="has-text-centered">Qty</th>
                            <th class="has-text-centered">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                        <tr v-for="item in $invoice->items" :key="item.id">
                            <td>
                                @if ($invoice->company->show_image && $item->product->image)
                                <figure class="image invoice-item-figure">
                                    <img src="{{ $item->product->image }}" alt="{{ $item->name }}">
                                </figure>
                                @endif
                                {{ $item->code }}
                                <br>
                                <small class="text-muted">{{ $item->name }}</small>
                            </td>
                            <td class="has-text-right">
                                <span class="right">{{ formatNumber($invoice->company->show_tax ? ($invoice->company->show_discount ? $item->price : $item->net_price) : ($invoice->company->show_discount
                                ? $item->price + $item->tax_amount : $item->unit_price)) }}</span>
                            </td>
                            @if ($invoice->company->show_discount)
                            <td class="has-text-right">
                                {{ formatNumber($item->discount_amount) }}
                            </td>
                            @endif @if ($invoice->company->show_tax)
                            <td>
                                @foreach($item->taxes as $tax)
                                <div v-for="tax in $item->taxes" :key="$item->id+'_'+tax.id" class="inline-block m-b-none">
                                    <small class="text-muted">{{ $tax->code }}</small>
                                    <span class="is-pulled-right right">{{ formatNumber($tax->pivot->amount) }}</span>
                                </div>
                                @endforeach
                            </td>
                            @endif
                            <td class="has-text-right">
                                <span class="right">{{ formatNumber($item->qty) }}</span>
                            </td>
                            <td class="has-text-right">
                                <strong class="right">{{ formatNumber($item->subtotal) }}</strong>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="net_total_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Sub Total</td>
                            <td class="item_r">{{ formatNumber($invoice->total-$invoice->product_tax_amount) }}</td>
                        </tr>
                        @if ($invoice->discount > 0)
                        <tr class="discount_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Discount</td>
                            <td class="item_r">{{ formatNumber($invoice->discount) }}</td>
                        </tr>
                        @endif {{--
                        <tr class="net_total_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Total</td>
                            <td class="item_r">{{ formatNumber($invoice->total) }}</td>
                        </tr> --}} @if ($invoice->total_tax_amount > 0)
                        <tr class="total_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Tax</td>
                            <td class="item_r">{{ formatNumber($invoice->total_tax_amount) }}</td>
                        </tr>
                        @endif
                        <tr class="total_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="total total_currency">Final (<span class="currency">{{ env('CURRENCY_CODE') }}</span>)</td>
                            <td class="total">{{ formatNumber($invoice->grand_total) }}</td>
                        </tr>
                    </tfoot>
                </table>


                @if($invoice->attributes->isNotEmpty())
                <div class="order-other">
                    <div class="comments">
                        <h2>Extra Information</h2>
                        @foreach($invoice->attributes as $attr) @if ($invoice->{$attr->slug} || $attr->type == 'boolean')
                        <div><strong>{{ $attr->name }}:</strong> {{ $attr->type == 'boolean' ? ($invoice->{$attr->slug} ? 'Yes'
                            : 'No') : $invoice->{$attr->slug} }}</div>
                        @endif @endforeach
                    </div>
                </div>
                @endif

                <div class="note-details">
                    <div class="comments">
                        <h2>Note:</h2> {{ $invoice->note }}
                    </div>
                </div>

                <div class="comments">
                    {{ $invoice->company->footer }}
                </div>

                <div class="order-footer">
                    This is a computer-generated document. No signature is required.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
