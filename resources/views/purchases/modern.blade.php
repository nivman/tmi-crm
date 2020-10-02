<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@php $col = 1; if ($purchase->company->show_discount) { $col++; } if ($purchase->company->show_tax) { $col++; }
@endphp

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Purchase Number {{ $purchase->id }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/modern.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="wrapper">
            <div class="order">
                <div class="order-header">
                    <img alt="{{ $purchase->company->name }}" class="logo screen" src="/images/default.png" />
                    <div class="vcard company-address">
                        <div class="fn org"><strong>{{ $purchase->company->name }}</strong>
                        </div>
                        <div class="adr">
                            <div class="street-address">
                                {{ $purchase->company->address }}<br> {{ $purchase->company->state_name }}, {{ $purchase->company->country_name
                                }} <br>
                            </div>
                            <div>Phone: {{ $purchase->company->phone }}</div>
                            <div>Email: {{ $purchase->company->email }}</div>
                            @for($i = 0; $i
                            < 5 ; $i++) @if(isset($purchase->company->extra['cf'.$i.'_label']) && isset($purchase->company->extra['cf'.$i.'_value']) && !empty($purchase->company->extra['cf'.$i.'_label'])
                                && !empty($purchase->company->extra['cf'.$i.'_value']))
                                <div>{{ $purchase->company->extra['cf'.$i.'_label'] }}: {{ $purchase->company->extra['cf'.$i.'_value']
                                    }}
                                </div>
                                @endif @endfor
                        </div>
                    </div>
                </div>

                <div class="order-info">
                    <h2>Purchase No. <strong>{{ $purchase->id }}</strong></h2>
                    <h3>Date: {{ \Carbon\Carbon::parse($purchase->date)->format(php_date_formate()) }}</h3>
                    <h3>Reference: {{ $purchase->reference }}</h3>
                </div>

                <div class="vcard client-details">
                    <div class="light">Ordered to:</div>
                    <div class="fn">{{ $purchase->vendor->name }}</div>
                    <div class="org">{{ $purchase->vendor->company }}</div>
                    <div class="adr">
                        <div class="street-address">
                            {{ $purchase->vendor->address }}<br>{{ $purchase->vendor->state_name }}, {{ $purchase->vendor->country_name
                            }}
                            <br>
                        </div>
                        <div>Phone: {{ $purchase->vendor->phone }}</div>
                        <div>Email: {{ $purchase->vendor->email }}</div>
                        @foreach($purchase->vendor->attributes as $attr) @if ($purchase->vendor->{$attr->slug})
                        <div>{{ $attr->name }}: {{ $purchase->vendor->{$attr->slug} }}</div>
                        @endif @endforeach
                    </div>
                </div>

                <table class="order-amount">
                    <thead>
                        <tr class="header_row">
                            <th>Item / Details</th>
                            <th class="has-text-centered">Price</th>
                            @if ($purchase->company->show_discount)
                            <th class="has-text-centered colfix">Discount</th>
                            @endif @if ($purchase->company->show_tax)
                            <th style="width:125px;" class="has-text-centered">Tax</th>
                            @endif
                            <th class="has-text-centered">Qty</th>
                            <th class="has-text-centered">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr v-for="item in $purchase->items" :key="item.id">
                            <td>
                                @if ($purchase->company->show_image && $item->product->image)
                                <figure class="image purchase-item-figure">
                                    <img src="{{ $item->product->image }}" alt="{{ $item->name }}">
                                </figure>
                                @endif
                                {{ $item->code }}
                                <br>
                                <small class="text-muted">{{ $item->name }}</small>
                            </td>
                            <td class="has-text-right">
                                <span class="right">{{ formatNumber($purchase->company->show_tax ? ($purchase->company->show_discount ? $item->cost : $item->net_cost) : ($purchase->company->show_discount
                                ? $item->cost + $item->tax_amount : $item->unit_cost)) }}</span>
                            </td>
                            @if ($purchase->company->show_discount)
                            <td class="has-text-right">
                                {{ formatNumber($item->discount_amount) }}
                            </td>
                            @endif @if ($purchase->company->show_tax)
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
                            <td class="item_r">{{ formatNumber($purchase->total-$purchase->product_tax_amount) }}</td>
                        </tr>
                        @if ($purchase->discount > 0)
                        <tr class="discount_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Discount</td>
                            <td class="item_r">{{ formatNumber($purchase->discount) }}</td>
                        </tr>
                        @endif {{--
                        <tr class="net_total_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Total</td>
                            <td class="item_r">{{ formatNumber($purchase->total) }}</td>
                        </tr> --}} @if ($purchase->total_tax_amount > 0)
                        <tr class="tax_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="item_r">Tax</td>
                            <td class="item_r">{{ formatNumber($purchase->total_tax_amount) }}</td>
                        </tr>
                        @endif
                        <tr class="total_tr">
                            <td colspan="{{$col}}">&nbsp;</td>
                            <td colspan="2" class="total total_currency">Final (<span class="currency">{{ env('CURRENCY_CODE') }}</span>)</td>
                            <td class="total">{{ formatNumber($purchase->grand_total) }}</td>
                        </tr>
                    </tfoot>
                </table>


                @if($purchase->attributes->isNotEmpty())
                <div class="order-other">
                    <div class="comments">
                        <h2>Extra Information</h2>
                        @foreach($purchase->attributes as $attr) @if ($purchase->{$attr->slug} || $attr->type == 'boolean')
                        <div><strong>{{ $attr->name }}:</strong> {{ $attr->type == 'boolean' ? ($purchase->{$attr->slug} ? 'Yes'
                            : 'No') : $purchase->{$attr->slug} }}</div>
                        @endif @endforeach
                    </div>
                </div>
                @endif

                <div class="note-details">
                    <div class="comments">
                        <h2>Note:</h2> {{ $purchase->note }}
                    </div>
                </div>

                <div class="comments">
                    {{ $purchase->company->footer }}
                </div>

                <div class="order-footer">
                    This is a computer-generated document. No signature is required.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
