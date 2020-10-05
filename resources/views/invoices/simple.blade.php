<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Invoice Number {{ $invoice->id }} - {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/simple.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="wrapper">
            <div class="order" id="invoice">
                <div class="order-header">
                    <div class="columns">
                        <div class="column is-6">
                            <div class="media">
                                <div class="media-left">
                                    <img class="media-object logo" src="{{ asset($invoice->company->logo) }}" />
                                </div>
                                <ul class="media-body list-unstyled">
                                    <li>
                                        <strong>{{ $invoice->company->name }}</strong>
                                    </li>
                                    <li>{{ $invoice->company->address }}</li>
                                    <li>{{ $invoice->company->phone }}</li>
                                    <li>{{ $invoice->company->email }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="column is-4 is-offset-2">
                            <h1>Invoice No.
                                <small>{{ $invoice->id }}</small>
                            </h1>

                            <h4 class="text-muted">Date: {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/yy') }}</h4>
                            <h4 class="text-muted">Reference: {{ $invoice->reference }}</h4>
                        </div>
                    </div>
                </div>
                <div class="order-body">
                    <div class="columns">
                        <div class="column is-6">
                            <div class="panel panel-border is-shadowless">
                                <p class="panel-heading">
                                    Company Details
                                </p>
                                <div class="panel-block">
                                    <dl class="dl-horizontal">
                                        <dt>Name :</dt>
                                        <dd>
                                            <strong>{{ $invoice->company->name }}</strong>
                                        </dd>
                                        <dt>Address :</dt>
                                        <dd>{{ $invoice->company->address.', '.$invoice->company->state_name.', ' .$invoice->company->country_name
                                            }}
                                        </dd>
                                        <dt>Phone :</dt>
                                        <dd>{{ $invoice->company->phone }}</dd>
                                        <dt>Email :</dt>
                                        <dd>{{ $invoice->company->email }}</dd>
                                        @for($i = 0; $i
                                        < 5 ; $i++) @if(isset($invoice->company->extra['cf'.$i.'_label']) && isset($invoice->company->extra['cf'.$i.'_value'])
                                            && !empty($invoice->company->extra['cf'.$i.'_label']) && !empty($invoice->company->extra['cf'.$i.'_value']))
                                            <dt>{{ $invoice->company->extra['cf'.$i.'_label'] }} :</dt>
                                            <dd>{{ $invoice->company->extra['cf'.$i.'_value'] }}</dd>
                                            @endif @endfor
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="panel panel-border is-shadowless">
                                <p class="panel-heading">
                                    Customer Details
                                </p>
                                <div class="panel-block">
                                    <dl class="dl-horizontal">
                                        <dt>Name :</dt>
                                        <dd><strong>{{ $invoice->customer->name }}</strong></dd>
                                        <dt>Company :</dt>
                                        <dd>{{ $invoice->customer->company }}</dd>
                                        <dt>Address :</dt>
                                        <dd>{{ $invoice->customer->address.', '.$invoice->customer->state_name.', ' .$invoice->customer->country_name
                                            }}
                                        </dd>
                                        <dt>Phone :</dt>
                                        <dd>{{ $invoice->customer->phone }}</dd>
                                        <dt>Email :</dt>
                                        <dd>{{ $invoice->customer->email }}</dd>
                                        @foreach($invoice->customer->attributes as $attr) @if ($invoice->customer->{$attr->slug})
                                        <dt>{{ $attr->name }} :</dt>
                                        <dd>{{ $invoice->customer->{$attr->slug} }}</dd>
                                        @endif @endforeach
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-border is-shadowless table-head-br">
                        <p class="panel-heading" style="border-bottom:0;">
                            Services / Products
                        </p>
                        <table class="table is-bordered is-fullwidth is-rounded is-narrow m-b-none">
                            <thead>
                                <tr>
                                    <th>Item / Details</th>
                                    <th class="has-text-centered colfix">Price</th>
                                    @if ($invoice->company->show_discount)
                                    <th style="min-width:85px;" class="has-text-centered colfix">Discount</th>
                                    @endif @if ($invoice->company->show_tax)
                                    <th style="min-width:125px;" class="has-text-centered colfix">Tax</th>
                                    @endif
                                    <th class="has-text-centered colfix">Qty</th>
                                    <th class="has-text-centered colfix">Total</th>
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
                                        <span class="right">{{ formatNumber($invoice->company->show_tax ? ($invoice->company->show_discount ? $item->price : $item->net_price) : ($invoice->company->show_discount ? $item->price + $item->tax_amount : $item->unit_price)) }}</span>
                                    </td>
                                    @if ($invoice->company->show_discount)
                                    <td class="has-text-right">

                                        <div class="inline-block m-b-none">
                                            <small class="text-muted">{{ $item->discount }}</small>
                                            <span class="is-pulled-right right">
                                                {{ formatNumber($item->discount_amount) }}
                                             </span>
                                        </div>
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
                        </table>
                    </div>
                    <div class="panel panel-border is-shadowless">
                        <table class="table is-bordered is-fullwidth is-rounded is-narrow m-b-none">
                            <thead>
                                <tr>
                                    <th width="20%">Sub Total</th>
                                    @if ($invoice->discount > 0)
                                    <th width="20%">Discount</th>
                                    @endif {{--
                                    <th width="20%">Total</th> --}} @if ($invoice->total_tax_amount > 0)
                                    <th width="20%">Tax</th>
                                    @endif
                                    <th width="20%">Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="rowtotal right">{{ formatNumber($invoice->total-$invoice->product_tax_amount) }}</td>
                                    @if ($invoice->discount > 0)
                                    <td class="rowtotal right">{{ formatNumber($invoice->discount) }}</td>
                                    @endif {{--
                                    <td class="rowtotal right">{{ formatNumber($invoice->total) }}</td> --}} @if ($invoice->total_tax_amount > 0)
                                    <td class="rowtotal right">{{ formatNumber($invoice->total_tax_amount) }}</td>
                                    @endif
                                    <td class="rowtotal right">{{ formatNumber($invoice->grand_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="columns">
                        <div class="column is-7">
                            @if ($invoice->note)
                            <div class="panel panel-border is-shadowless" v-if="$invoice->note">
                                <p class="panel-heading">Comment / Note</p>
                                <div class="panel-block">
                                    {{ $invoice->note }}
                                </div>
                            </div>
                            @endif
                            <!-- <div class="panel panel-border is-shadowless">
                                <p class="panel-heading">
                                    Payment Method
                                </p>
                                <div class="panel-block">
                                    <div class="columns">
                                        <div class="column is-12">
                                            <p>For your convenience, you may deposite the final amount at one of our banks</p>
                                            <ul class="list-unstyled">
                                                <li>Alpha Bank -
                                                    <span class="right">MO123456789456123</span>
                                                </li>
                                                <li>Beta Bank -
                                                    <span class="right">MO123456789456123</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                        <div class="column is-5">
                            @if($invoice->attributes->isNotEmpty())
                            <div class="panel panel-border is-shadowless">
                                <p class="panel-heading">
                                    Extra Information
                                </p>
                                <div class="panel-block">
                                    <dl class="dl-horizontal">
                                        @foreach($invoice->attributes as $attr) @if ($invoice->{$attr->slug} || $attr->type == 'boolean')
                                        <dt>{{ $attr->name }} :</dt>
                                        <dd>{{ $attr->type == 'boolean' ? ($invoice->{$attr->slug} ? 'Yes' : 'No') : ($attr->type
                                            == 'datetime' ? \Carbon\Carbon::parse($invoice->{$attr->slug})->format(php_date_formate()."
                                            H:i") : $invoice->{$attr->slug}) }}
                                        </dd>
                                        @endif @endforeach
                                    </dl>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="order-footer">
                    {{ $invoice->company->footer }}
                    <br>This is a computer-generated document. No signature is required.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
