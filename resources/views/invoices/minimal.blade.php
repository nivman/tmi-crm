<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Number {{ $invoice->id }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/minimal.css') }}" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <div class="order">

            <table width="650" border="0" cellspacing="0" cellpadding="0">
                <tr>
                    <td align="left" valign="top" width="55%">
                        <span>
                            <i><small>Invoiced To</small></i><br>
                            <big>
                                <strong>{{ $invoice->customer->name }}</strong><br>
                                {{ $invoice->customer->company }}
                            </big><br>
                            {{ $invoice->customer->address }}<br>
                            {{ $invoice->customer->state_name }}, {{ $invoice->customer->country_name }}<br>
                            Phone: {{ $invoice->customer->phone }}<br>
                            Email: {{ $invoice->customer->email }}<br>
                            @foreach($invoice->customer->attributes as $attr)
                            @if($invoice->customer->{$attr->slug})
                            {{ $attr->name }}: {{ $invoice->customer->{$attr->slug} }}<br>
                            @endif @endforeach
                        </span>
                    </td>
                    <td align="right">
                        <span>
                            <strong><big>{{ $invoice->company->name }}</big></strong><br>
                            {{ $invoice->company->address }}<br>
                            {{ $invoice->company->state_name }}, {{ $invoice->company->country_name }}<br>
                            Phone: {{ $invoice->company->phone }}<br>
                            Email: {{ $invoice->company->email }}<br>
                            @for($i = 0; $i < 5 ; $i++) @if(isset($invoice->company->extra['cf'.$i.'_label']) && isset($invoice->company->extra['cf'.$i.'_value']) && !empty($invoice->company->extra['cf'.$i.'_label'])
                            && !empty($invoice->company->extra['cf'.$i.'_value']))
                                {{ $invoice->company->extra['cf'.$i.'_label'] }}: {{ $invoice->company->extra['cf'.$i.'_value'] }}<br>
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
                        <strong><span>TAX INVOICE</span></strong>
                    </td>
                    <td align="right" valign="top">
                        <span><strong>Invoice No: {{ $invoice->id }}</strong><br></span>
                        <span>Date: {{ \Carbon\Carbon::parse($invoice->date)->format(php_date_formate()) }}<br></span>
                        <span>Reference: {{ $invoice->reference }}<br></span>
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
                                    @if ($invoice->company->show_discount)
                                    <th align="center">Discount</th>
                                    @endif @if ($invoice->company->show_tax)
                                    <th style="min-width:85px;" align="center">Tax</th>
                                    @endif
                                    <th align="center">Qty</th>
                                    <th align="center">Total</th>
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
                                    <td align="right">
                                        <span>{{ formatNumber($invoice->company->show_tax ? ($invoice->company->show_discount ? $item->price : $item->net_price) : ($invoice->company->show_discount
                                        ? $item->price + $item->tax_amount : $item->unit_price)) }}</span>
                                    </td>
                                    @if ($invoice->company->show_discount)
                                    <td align="right">
                                        {{ formatNumber($item->discount_amount) }}
                                    </td>
                                    @endif @if ($invoice->company->show_tax)
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
                                    <td width="100%" valign="top">{{ $invoice->note ? '<strong>Note:</strong><br>'.$invoice->note : '' }}</td>
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
                                    <td align="right"><strong>{{ formatNumber($invoice->total-$invoice->product_tax_amount) }}</strong></td>
                                </tr>
                                @if ($invoice->discount > 0)
                                <tr>
                                    <td calign="left">Discount</td>
                                    <td align="right"><strong>{{ formatNumber($invoice->discount) }}</strong></td>
                                </tr>
                                @endif {{--
                                <tr>
                                    <td calign="left">Total</td>
                                    <td align="right"><strong>{{ formatNumber($invoice->total) }}</strong></td>
                                </tr> --}} @if ($invoice->total_tax_amount > 0)
                                <tr>
                                    <td calign="left">Tax</td>
                                    <td align="right"><strong>{{ formatNumber($invoice->total_tax_amount) }}</strong></td>
                                </tr>
                                @endif
                                <tr>
                                    <td calign="left"><big><strong>Grand Total</strong></big></td>
                                    <td align="right"><big><strong>{{ formatNumber($invoice->grand_total) }}</strong></big></td>
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
                        {{ $invoice->company->footer }}
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
