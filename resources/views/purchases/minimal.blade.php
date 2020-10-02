<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Purchase Number {{ $purchase->id }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/minimal.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="order">

            <table width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="top" width="55%">
                        <span>
                            <i><small>Ordered To</small></i><br>
                            <big>
                                <strong>{{ $purchase->vendor->name }}</strong><br>
                                {{ $purchase->vendor->company }}
                            </big><br>
                            {{ $purchase->vendor->address }}<br>
                            {{ $purchase->vendor->state_name }}, {{ $purchase->vendor->country_name }}<br>
                            Phone: {{ $purchase->vendor->phone }}<br>
                            Email: {{ $purchase->vendor->email }}<br>
                            @foreach($purchase->vendor->attributes as $attr)
                            @if($purchase->vendor->{$attr->slug})
                            {{ $attr->name }}: {{ $purchase->vendor->{$attr->slug} }}<br>
                            @endif @endforeach
                        </span>
                    </td>
                    <td align="right">
                        <span>
                            <strong><big>{{ $purchase->company->name }}</big></strong><br>
                            {{ $purchase->company->address }}<br>
                            {{ $purchase->company->state_name }}, {{ $purchase->company->country_name }}<br>
                            Phone: {{ $purchase->company->phone }}<br>
                            Email: {{ $purchase->company->email }}<br>
                            @for($i = 0; $i < 5 ; $i++) @if(isset($purchase->company->extra['cf'.$i.'_label']) && isset($purchase->company->extra['cf'.$i.'_value']) && !empty($purchase->company->extra['cf'.$i.'_label'])
                            && !empty($purchase->company->extra['cf'.$i.'_value']))
                                {{ $purchase->company->extra['cf'.$i.'_label'] }}: {{ $purchase->company->extra['cf'.$i.'_value'] }}<br>
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
                        <strong><span>PURCHASE ORDER</span></strong>
                    </td>
                    <td align="right" valign="top">
                        <span><strong>Purchase No: {{ $purchase->id }}</strong><br></span>
                        <span>Date: {{ \Carbon\Carbon::parse($purchase->date)->format(php_date_formate()) }}<br></span>
                        <span>Reference: {{ $purchase->reference }}<br></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                </tr>
            </table>

            <table width="650" class="borderless" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <table width="100%" class="bordered" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th align="center">Item / Details</th>
                                    <th align="center">Price</th>
                                    @if ($purchase->company->show_discount)
                                    <th align="center">Discount</th>
                                    @endif @if ($purchase->company->show_tax)
                                    <th style="min-width:85px;" align="center">Tax</th>
                                    @endif
                                    <th align="center">Qty</th>
                                    <th align="center">Total</th>
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
                                    <td align="right">
                                        <span>{{ formatNumber($purchase->company->show_tax ? ($purchase->company->show_discount ? $item->cost : $item->net_cost) : ($purchase->company->show_discount
                                        ? $item->cost + $item->tax_amount : $item->unit_cost)) }}</span>
                                    </td>
                                    @if ($purchase->company->show_discount)
                                    <td align="right">
                                        {{ formatNumber($item->discount_amount) }}
                                    </td>
                                    @endif @if ($purchase->company->show_tax)
                                    <td>
                                        <table width="100%" class="borderless" border="0">
                                            @foreach($item->taxes as $tax)
                                            <tr v-for="tax in $item->taxes" :key="$item->id+'_'+tax.id" class="inline-block m-b-none">
                                                <td align="left">{{ $tax->code }}</td>
                                                <td align="right">{{ formatNumber($tax->pivot->amount) }}</td>
                                            </tr>
                                            @endforeach
                                        </table>
                                    </td>
                                    @endif
                                    <td align="center">
                                        <span>{{ formatNumber($item->qty) }}</span>
                                    </td>
                                    <td align="right">
                                        <strong>{{ formatNumber($item->subtotal) }}</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            <table width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td width="60%" valign="top">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td width="100%" valign="top">{{ $purchase->note ? '<strong>Note:</strong><br>'.$purchase->note : '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width="5%"></td>
                    <td width="30%">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td calign="left">Sub Total</td>
                                    <td align="right"><strong>{{ formatNumber($purchase->total-$purchase->product_tax_amount) }}</strong></td>
                                </tr>
                                @if ($purchase->discount > 0)
                                <tr>
                                    <td calign="left">Discount</td>
                                    <td align="right"><strong>{{ formatNumber($purchase->discount) }}</strong></td>
                                </tr>
                                @endif {{--
                                <tr>
                                    <td calign="left">Total</td>
                                    <td align="right"><strong>{{ formatNumber($purchase->total) }}</strong></td>
                                </tr> --}} @if ($purchase->total_tax_amount > 0)
                                <tr>
                                    <td calign="left">Tax</td>
                                    <td align="right"><strong>{{ formatNumber($purchase->total_tax_amount) }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td calign="left"><big><strong>Grand Total</strong></big></td>
                                    <td align="right"><big><strong>{{ formatNumber($purchase->grand_total) }}</strong></big></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        {{ $purchase->company->footer }}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="center">
                        <br>This is a computer-generated document. No signature is required.
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>
