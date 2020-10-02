<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="{{ asset('icon-48.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Purchase Number {{ $purchase->id }}- {{ config('app.name', 'Business Manager') }}</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="{{ mix('css/simple.css') }}" rel="stylesheet">
</head>

<body>
    <div id="app">
        <div class="wrapper">
            <div class="order" id="purchase">
                <div class="order-header">
                    <div class="columns">
                        <div class="column is-6">
                            <div class="media">
                                <div class="media-left">
                                    <img class="media-object logo" src="{{ asset($purchase->company->logo) }}" />
                                </div>
                                <ul class="media-body list-unstyled">
                                    <li>
                                        <strong>{{ $purchase->company->name }}</strong>
                                    </li>
                                    <li>{{ $purchase->company->address}}</li>
                                    <li>{{ $purchase->company->phone }}</li>
                                    <li>{{ $purchase->company->email }}</li>
                                </ul>
                            </div>
                        </div>
                        <div class="column is-4 is-offset-2">
                            <h1>Purchase No.
                                <small>{{ $purchase->id }}</small>
                            </h1>
                            <h4 class="text-muted">Date: {{ \Carbon\Carbon::parse($purchase->date)->format(php_date_formate()) }}</h4>
                            <h4 class="text-muted">Reference: {{ $purchase->reference }}</h4>
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
                                            <strong>{{ $purchase->company->name }}</strong>
                                        </dd>
                                        <dt>Address :</dt>
                                        <dd>{{ $purchase->company->address.', '.$purchase->company->state_name.', ' .$purchase->company->country_name
                                            }}
                                        </dd>
                                        <dt>Phone :</dt>
                                        <dd>{{ $purchase->company->phone }}</dd>
                                        <dt>Email :</dt>
                                        <dd>{{ $purchase->company->email }}</dd>
                                        @for($i = 0; $i
                                        < 5 ; $i++) @if(isset($purchase->company->extra['cf'.$i.'_label']) && isset($purchase->company->extra['cf'.$i.'_value'])
                                            && !empty($purchase->company->extra['cf'.$i.'_label']) && !empty($purchase->company->extra['cf'.$i.'_value']))
                                            <dt>{{ $purchase->company->extra['cf'.$i.'_label'] }} :</dt>
                                            <dd>{{ $purchase->company->extra['cf'.$i.'_value'] }}</dd>
                                            @endif @endfor
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="column is-6">
                            <div class="panel panel-border is-shadowless">
                                <p class="panel-heading">
                                    Vendor Details
                                </p>
                                <div class="panel-block">
                                    <dl class="dl-horizontal">
                                        <dt>Name :</dt>
                                        <dd><strong>{{ $purchase->vendor->name }}</strong></dd>
                                        <dt>Company :</dt>
                                        <dd>{{ $purchase->vendor->company }}</dd>
                                        <dt>Address :</dt>
                                        <dd>{{ $purchase->vendor->address.', '.$purchase->vendor->state_name.', ' .$purchase->vendor->country_name
                                            }}
                                        </dd>
                                        <dt>Phone :</dt>
                                        <dd>{{ $purchase->vendor->phone }}</dd>
                                        <dt>Email :</dt>
                                        <dd>{{ $purchase->vendor->email }}</dd>
                                        @foreach($purchase->vendor->attributes as $attr) @if ($purchase->vendor->{$attr->slug})
                                        <dt>{{ $attr->name }} :</dt>
                                        <dd>{{ $purchase->vendor->{$attr->slug} }}</dd>
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
                                    @if ($purchase->company->show_discount)
                                    <th style="min-width:85px;" class="has-text-centered colfix">Discount</th>
                                    @endif @if ($purchase->company->show_tax)
                                    <th style="min-width:125px;" class="has-text-centered colfix">Tax</th>
                                    @endif
                                    <th class="has-text-centered colfix">Qty</th>
                                    <th class="has-text-centered colfix">Total</th>
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
                        </table>
                    </div>
                    <div class="panel panel-border is-shadowless">
                        <table class="table is-bordered is-fullwidth is-rounded is-narrow m-b-none">
                            <thead>
                                <tr>
                                    <th width="20%" class="text-center">Sub Total</th>
                                    @if ($purchase->discount > 0)
                                    <th width="20%" class="text-center">Discount</th>
                                    @endif {{--
                                    <th width="20%" class="text-center">Total</th> --}} @if ($purchase->total_tax_amount > 0)
                                    <th width="20%" class="text-center">Tax</th>
                                    @endif
                                    <th width="20%" class="text-center">Final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center rowtotal right">{{ formatNumber($purchase->total-$purchase->product_tax_amount) }}</td>
                                    @if ($purchase->discount > 0)
                                    <td class="text-center rowtotal right">{{ formatNumber($purchase->discount) }}</td>
                                    @endif {{--
                                    <td class="text-center rowtotal right">{{ formatNumber($purchase->total) }}</td> --}} @if ($purchase->total_tax_amount > 0)
                                    <td class="text-center rowtotal right">{{ formatNumber($purchase->total_tax_amount) }}</td>
                                    @endif
                                    <td class="text-center rowtotal right">{{ formatNumber($purchase->grand_total) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="columns">
                        <div class="column is-7">
                            @if ($purchase->note)
                            <div class="panel panel-border is-shadowless" v-if="$purchase->note">
                                <p class="panel-heading">Comment / Note</p>
                                <div class="panel-block">
                                    {{ $purchase->note }}
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
                            @if($purchase->attributes->isNotEmpty())
                            <div class="panel panel-border is-shadowless">
                                <p class="panel-heading">
                                    Extra Information
                                </p>
                                <div class="panel-block">
                                    <dl class="dl-horizontal">
                                        @foreach($purchase->attributes as $attr) @if ($purchase->{$attr->slug} || $attr->type == 'boolean')
                                        <dt>{{ $attr->name }} :</dt>
                                        <dd>{{ $attr->type == 'boolean' ? ($purchase->{$attr->slug} ? 'Yes' : 'No') : ($attr->type
                                            == 'datetime' ? \Carbon\Carbon::parse($purchase->{$attr->slug})->format(php_date_formate()."
                                            H:i") : $purchase->{$attr->slug}) }}
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
                    {{ $purchase->company->footer }}
                    <br>This is a computer-generated document. No signature is required.
                </div>
            </div>
        </div>
    </div>
</body>

</html>
