@extends('Admin.layouts.app')

@section('title', translate('Order Details'))

@push('css_or_js')
    <style>
        figure{
            margin-bottom: -1px;
        }
    </style>
    <link rel="stylesheet" href="{{asset('assets/admin/css/lightbox.min.css')}}">
@endpush
@section('content')

    <div class="content container-fluid">
        <div class="page-header d-flex justify-content-between">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/order.png')}}" class="w--20" alt="">
                </span>
                <span>
                    {{translate('order details')}}
                </span>
            </h1>
        </div>

        <div class="row" id="printableArea">
            <div class="col-lg-8 order-print-area-left">
                <div class="card mb-3 mb-lg-5">
                    <div class="card-header flex-wrap align-items-start border-0">
                        <div class="order-invoice-left">
                            <h1 class="page-header-title">
                                <span class="mr-3">
                                    {{translate('order ID')}} #{{$order['id']}}
                                </span>
                                <span class="badge badge-soft-info py-2 px-3">
                                    {{$order->vender_id != null ? 'Vendor :- '.$order->vendororders->name : translate('Admin')}}
                                </span>
                            </h1>
                            <span>
                                <i class="tio-date-range"></i>
                                {{date('d M Y',strtotime($order['created_at']))}} {{ date(config('time_format'), strtotime($order['created_at'])) }}
                            </span>
                            <!-- <a class="btn btn--info" href="{{route('admin.orders.edit_item',['id'=>$order['id']])}}" style="margin-top: 40px; width:160px;">
                                <i class="tio-edit mr-1"></i>
                                {{translate('Edit')}} {{translate('Items')}}
                            </a> -->

                            <!-- <h6 class="mt-5">
                                {{translate('Edit Status')}} :
                                @if($order['editable']== 0)
                                    <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                                        {{translate('Freash')}}
                                    </span>
                                @elseif($order['editable']== 1)
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                                        {{translate('Edit processing')}}
                                    </span>
                                @elseif($order['editable']== 2)
                                    <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                                        {{translate('Edit Accepted')}}
                                    </span>
                                @elseif($order['editable']== 3)
                                    <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                        {{translate('edit rejected')}}
                                    </span>
                                @endif
                            </h6> -->
                        </div>
                        <div class="order-invoice-right mt-3 mt-sm-0">
                            <div class="text-right mt-3 order-invoice-right-contents text-capitalize">
                                <h6 class="text-capitalize">
                                    <span class="text-body mr-2">
                                        {{translate('payment')}} {{translate('method')}}:
                                    </span>
                                    <span class="text--title font-bold">
                                        {{ translate(str_replace('_',' ',$order['payment_method']))}}
                                    </span>
                                </h6>
                                <h6>
                                    <span class="text-body mr-2">{{ translate('payment') }} {{ translate('status') }} : </span>

                                    @if($order['payment_status']=='paid')
                                        <span class="badge badge-soft-success ml-sm-3">
                                            {{translate('paid')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger ml-sm-3">
                                            {{translate($order['payment_status'])}}
                                        </span>
                                    @endif
                                </h6>
                                <h6 class="text-capitalize">
                                    <span class="text-body">
                                        {{translate('order')}} {{translate('type')}}:
                                    </span>
                                    <label class="badge badge-soft-primary ml-3">
                                        {{ translate(str_replace('_',' ',$order['order_type'])) }}
                                    </label>
                                </h6>

                                @if($order['parent_order_id'] != null)
                                    <h6 class="text-capitalize">
                                        <span class="text-body">
                                            {{translate('parent')}} {{translate('order')}} {{translate('id')}}:
                                        </span>
                                        <label class="badge badge-soft-primary ml-3">
                                            <a href="{{route('admin.orders.details',['id'=>$order['parent_order_id']])}}">
                                                {{$order['parent_order_id']}}
                                            </a>
                                        </label>
                                    </h6>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        @php($subTotal=0)
                        @php($amount=0)
                        @php($totalTax=0)
                        @php($total_dis_on_pro=0)
                        @php($totalItemDiscount=0)
                        @php($price_after_discount=0)
                        @php($updatedTotalTax=0)
                        @php($advance=0)
                        @php($vatStatus = '')
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{translate('SL')}}</th>
                                        @if($order['editable']== 1 || $order['editable']== 3)
                                            <th class="border-0">{{translate('Replace')}}</th>
                                        @endif
                                        <th class="border-0">{{translate('Item')}}</th>
                                        <th class="border-0">{{translate('details')}}</th>
                                        <th class="border-0 text-right">{{translate('Price')}}</th>
                                        <th class="border-0 text-right">{{translate('Discount')}}</th>
                                        <th class="text-right border-0">{{translate('Total Price')}}</th>
                                    </tr>
                                </thead>
                                @if($order['order_type'] == 'goods')
                                    @foreach($order->OrderDetails as $detail)
                                        @if($detail->product_details !=null)
                                            @php($product = json_decode($detail->product_details, true))
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                @if($order['editable']== 1 || $order['editable']== 3)
                                                    @if($detail->alt_product_id != 0)
                                                        @php($pro_d = App\Model\Product::find($detail->alt_product_id))
                                                        <td>
                                                            <div class='media media--sm'>
                                                                <div class='avatar avatar-xl mr-3'>
                                                                    @if($pro_d['image'] != null )
                                                                    <img class="img-fluid rounded aspect-ratio-1"
                                                                        src="{{ $pro_d->identityImageFullPath[0] }}"
                                                                        alt="{{translate('Image Description')}}">
                                                                    @else
                                                                        <img
                                                                        src="{{asset('assets/admin/img/160x160/2.png')}}"
                                                                        class="img-fluid rounded aspect-ratio-1"
                                                                        >
                                                                    @endif
                                                                </div>
                                                                <div class='media-body'>
                                                                    <h5 class='line--limit-1' title="{{$pro_d['name']}}">
                                                                        {{$pro_d['name']}}
                                                                    </h5>
                                                                    <h5 class='mt-1'>
                                                                        <span class='text-body'>
                                                                            {{translate('Unit')}}
                                                                        </span>
                                                                        : {{$pro_d['unit']}} 
                                                                    </h5>
                                                                    <h5 class='mt-1'>
                                                                        <span class='text-body'>
                                                                            {{translate('Unit Price')}}
                                                                        </span>
                                                                        : {{$pro_d['price']}} 
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td class="text-right">
                                                            <h6>This Item is not Replaced</h6>
                                                        </td>
                                                    @endif
                                                @endif
                                                <td>
                                                    <div class="media media--sm">
                                                        <div class="avatar avatar-xl mr-3">
                                                            @if($detail->product && $detail->product['image'] != null )
                                                            <img class="img-fluid rounded aspect-ratio-1"
                                                                src="{{ asset(json_decode($detail->product->image)[0]) }}"
                                                                alt="{{translate('Image Description')}}">
                                                            @else
                                                                <img
                                                                src="{{asset('assets/admin/img/160x160/2.png')}}"
                                                                class="img-fluid rounded aspect-ratio-1"
                                                                >
                                                            @endif
                                                        </div>
                                                        <div class="media-body">
                                                            <h5 class="line--limit-1" title="{{$product['name']}}">{{$product['name']}}</h5>
                                                            @if(count(json_decode($detail['variation'],true)) > 0)
                                                                @foreach(json_decode($detail['variation'],true)[0]??json_decode($detail['variation'],true) as $key1 =>$variation)
                                                                    <div class="font-size-sm text-body text-capitalize">
                                                                        @if($variation != null)
                                                                        <span>{{$key1}} :  </span>
                                                                        @endif
                                                                        <span class="font-weight-bold">{{$variation}}</span>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('Unit')}}
                                                        </span>
                                                        : {{$detail['unit']}} 
                                                    </h5>
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('Unit Price')}}
                                                        </span> 
                                                        : {{$detail['price']}} 
                                                    </h5>
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('Advance')}}
                                                        </span> 
                                                        : {{$detail['advance_payment']}} 
                                                    </h5>
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('QTY')}}
                                                        </span> 
                                                        : {{$detail['quantity']}} 
                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    <h6>{{ Helpers_set_symbol($detail['price'] * $detail['quantity']) }}</h6>
                                                </td>
                                                <td class="text-right">
                                                    <h6>{{ Helpers_set_symbol($detail['discount_on_product'] * $detail['quantity']) }}</h6>
                                                </td>
                                                <td class="text-right">
                                                    @php($amount+=$detail['price']*$detail['quantity'])
                                                    @php($totalTax+=$detail['tax_amount']*$detail['quantity'])
                                                    @php($updatedTotalTax+= $detail['gst_status'] === 'included' ? 0 : $detail['tax_amount'] * $detail['quantity'])
                                                    @php($vatStatus = $detail['gst_status'])
                                                    @php($totalItemDiscount += $detail['discount_on_product'] * $detail['quantity'])
                                                    @php($price_after_discount+=$amount-$totalItemDiscount)
                                                    @php($subTotal+=$price_after_discount)
                                                    <h5>
                                                        {{ Helpers_set_symbol(($detail['price'] * $detail['quantity']) - ($detail['discount_on_product'] * $detail['quantity'])) }}
                                                    </h5>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @elseif($order['order_type'] == 'amc')
                                    @foreach($order->OrderDetails as $detail)
                                        @if($detail->product_details !=null)
                                            @php($product = json_decode(json_decode($detail->product_details, true)['service_details'],true))
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                @if($order['editable']== 1 || $order['editable']== 3)
                                                    @if($detail->alt_product_id != 0)
                                                        @php($pro_d = App\Model\Product::find($detail->alt_product_id))
                                                        <td>
                                                            <div class='media media--sm'>
                                                                <div class='avatar avatar-xl mr-3'>
                                                                    @if($pro_d['image'] != null )
                                                                    <img class="img-fluid rounded aspect-ratio-1"
                                                                        src="{{ $pro_d->identityImageFullPath[0] }}"
                                                                        alt="{{translate('Image Description')}}">
                                                                    @else
                                                                        <img
                                                                        src="{{asset('assets/admin/img/160x160/2.png')}}"
                                                                        class="img-fluid rounded aspect-ratio-1"
                                                                        >
                                                                    @endif
                                                                </div>
                                                                <div class='media-body'>
                                                                    <h5 class='line--limit-1' title="{{$pro_d['name']}}">
                                                                        {{$pro_d['name']}}
                                                                    </h5>
                                                                    <h5 class='mt-1'>
                                                                        <span class='text-body'>
                                                                            {{translate('Price')}}
                                                                        </span>
                                                                        : {{$pro_d['price']}} 
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td class="text-right">
                                                            <h6>This Item is not Replaced</h6>
                                                        </td>
                                                    @endif
                                                @endif
                                                <td>
                                                    <div class="media media--sm">
                                                        <div class="avatar avatar-xl mr-3">
                                                            @if($detail->product && $detail->product['image'] != null )
                                                            <img class="img-fluid rounded aspect-ratio-1" src="{{ asset(json_decode($detail->product->image)[0]) }}" alt="{{translate('Image Description')}}">
                                                            @else
                                                                <img src="{{asset('assets/admin/img/160x160/2.png')}}" class="img-fluid rounded aspect-ratio-1" />
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('Name')}}
                                                        </span> 
                                                        : {{$product['name']}} 
                                                    </h5>
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('QTY')}}
                                                        </span> 
                                                        : {{$detail['quantity']}} 
                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    <h6>--</h6>
                                                </td>
                                                <td class="text-right">
                                                    <h6>--</h6>
                                                </td>
                                                <td class="text-right">
                                                    <h6>--</h6>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    @foreach($order->OrderDetails as $detail)
                                        @if($detail->product_details !=null)
                                            @php($product = json_decode($detail->product_details, true))
                                            <tr>
                                                <td>
                                                    {{$loop->iteration}}
                                                </td>
                                                @if($order['editable']== 1 || $order['editable']== 3)
                                                    @if($detail->alt_product_id != 0)
                                                        @php($pro_d = App\Model\Product::find($detail->alt_product_id))
                                                        <td>
                                                            <div class='media media--sm'>
                                                                <div class='avatar avatar-xl mr-3'>
                                                                    @if($pro_d['image'] != null )
                                                                    <img class="img-fluid rounded aspect-ratio-1"
                                                                        src="{{ $pro_d->identityImageFullPath[0] }}"
                                                                        alt="{{translate('Image Description')}}">
                                                                    @else
                                                                        <img
                                                                        src="{{asset('assets/admin/img/160x160/2.png')}}"
                                                                        class="img-fluid rounded aspect-ratio-1"
                                                                        >
                                                                    @endif
                                                                </div>
                                                                <div class='media-body'>
                                                                    <h5 class='line--limit-1' title="{{$pro_d['name']}}">
                                                                        {{$pro_d['name']}}
                                                                    </h5>
                                                                    <h5 class='mt-1'>
                                                                        <span class='text-body'>
                                                                            {{translate('Price')}}
                                                                        </span>
                                                                        : {{$pro_d['price']}} 
                                                                    </h5>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td class="text-right">
                                                            <h6>This Item is not Replaced</h6>
                                                        </td>
                                                    @endif
                                                @endif
                                                <td>
                                                    <div class="media media--sm">
                                                        <div class="avatar avatar-xl mr-3">
                                                            @if($detail->product && $detail->product['image'] != null )
                                                                <img class="img-fluid rounded aspect-ratio-1" src="{{ asset(json_decode($detail->product->image)[0]) }}" alt="{{translate('Image Description')}}">
                                                            @else
                                                                <img src="{{asset('assets/admin/img/160x160/2.png')}}" class="img-fluid rounded aspect-ratio-1" >
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($order['parent_order_id'] != null)
                                                        <h5 class="mt-1">
                                                            <span class="text-body">
                                                                {{translate('Name')}}
                                                            </span> 
                                                            : {{$product['name']}} 
                                                        </h5>
                                                    @else
                                                        <h5 class="mt-1">
                                                            <span class="text-body">
                                                                {{translate('Price')}}
                                                            </span> 
                                                            : {{$detail['price']}} 
                                                        </h5>
                                                    @endif
                                                    <h5 class="mt-1">
                                                        <span class="text-body">
                                                            {{translate('QTY')}}
                                                        </span> 
                                                        : {{$detail['quantity']}} 
                                                    </h5>
                                                </td>
                                                <td class="text-right">
                                                    @if($order['parent_order_id'] != null)
                                                        <h6>--</h6>
                                                    @else
                                                        <h6>{{ Helpers_set_symbol($detail['price']) }}</h6>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if($order['parent_order_id'] != null)
                                                        <h6>--</h6>
                                                    @else
                                                        <h6>{{ Helpers_set_symbol($detail['discount_on_product']) }}</h6>
                                                    @endif
                                                    
                                                </td>
                                                <td class="text-right">
                                                    @php($amount+=$detail['price'])
                                                    @php($totalTax+=$detail['tax_amount'])
                                                    @php($updatedTotalTax+= $detail['gst_status'] === 'included' ? 0 : $detail['tax_amount'])
                                                    @php($vatStatus = $detail['gst_status'])
                                                    @php($totalItemDiscount += $detail['discount_on_product'])
                                                    @php($price_after_discount+=$amount-$totalItemDiscount)
                                                    @php($subTotal+=$price_after_discount)
                                                    @if($order['parent_order_id'] != null)
                                                        <h6>--</h6>
                                                    @else
                                                        <h5>
                                                            {{ Helpers_set_symbol($detail['price'] - $detail['discount_on_product']) }}
                                                        </h5>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif
                                <tr>
                                    <td colspan="12" class="td-p-0">
                                        <hr class="m-0" >
                                    </td>
                                </tr>
                            </table>
                        </div>

                        @if($order['parent_order_id'] == null)
                            @if($order['order_type'] != 'amc')
                                <div class="row justify-content-md-end mb-3 mt-4">
                                    <div class="col-md-9 col-lg-8">
                                        <dl class="row text-right justify-content-end">
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('items')}} {{translate('price')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                    {{--{{ Helpers_set_symbol($subTotal) }}--}}
                                                    {{ Helpers_set_symbol($amount) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Item Discount')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                - {{ Helpers_set_symbol($totalItemDiscount) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Sub Total')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol($total = $amount-$totalItemDiscount) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('GST')}} {{ $order['tax_type'] == 'included' ? (translate('included')) : (translate('excluded')) }}:
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol($totalTax) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Advance')}}
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                - {{ Helpers_set_symbol($order['advance_payment']) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('delivery')}} {{translate('fee')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                @if($order['order_type']=='self_pickup')
                                                    @php($del_c=0)
                                                @else
                                                    @php($del_c=$order['delivery_charge'])
                                                @endif
                                                {{ Helpers_set_symbol($del_c) }}
                                            </dd>

                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Coupon')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                - {{ Helpers_set_symbol($order['coupon_amount']) }}
                                                <hr>
                                            </dd>

                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('total')}}:
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol($total+$del_c+$updatedTotalTax-$order['coupon_discount_amount']-$order['extra_discount']-$order['advance_payment']-$order['coupon_amount']) }}
                                                <hr>
                                            </dd>
                                            @if ($order->partial_payment != null)
                                                @php($partial_payment = json_decode($order->partial_payment,true))
                                                <dt class="col-6 text-left">
                                                    <div class="ml-auto max-w-130px">
                                                        <span>{{translate('Paid By')}} ({{'Wallet'}})</span>
                                                        <span>:</span>
                                                    </div>
                                                </dt>
                                                <dd class="col-6 col-xl-5 pr-5">
                                                    {{ Helpers_set_symbol($partial_payment['wallet_applied']) }}
                                                </dd>
                                                <?php
                                                    $due_amount = 0;
                                                    $due_amount = $order->grand_total;
                                                ?>
                                                <dt class="col-6 text-left">
                                                    <div class="ml-auto max-w-130px">
                                                    <span>
                                                        {{translate('Due Amount')}}</span>
                                                        <span>:</span>
                                                    </div>
                                                </dt>
                                                <dd class="col-6 col-xl-5 pr-5">
                                                    {{ Helpers_set_symbol($total+$del_c+$updatedTotalTax-$order['coupon_discount_amount']-$order['advance_payment']-$order['coupon_amount']-$partial_payment['wallet_applied']) }}
                                                </dd>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            @else 
                                <div class="row justify-content-md-end mb-3 mt-4">
                                    <div class="col-md-9 col-lg-8">
                                        <dl class="row text-right justify-content-end">
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('items')}} {{translate('price')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol($order['item_total']) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Item Discount')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                - {{ Helpers_set_symbol($order['total_discount']) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Sub Total')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol($total = $order['item_total'] - $order['total_discount']) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('GST')}} {{ $order['tax_type'] == 'included' ? (translate('included')) : (translate('excluded')) }}:
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol($order['total_tax_amount']) }}
                                            </dd>
                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('Coupon')}} :
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                - {{ Helpers_set_symbol($order['coupon_amount']) }}
                                                <hr>
                                            </dd>

                                            <dt class="col-6 text-left">
                                                <div class="ml-auto max-w-130px">
                                                    {{translate('total')}}:
                                                </div>
                                            </dt>
                                            <dd class="col-6 col-xl-5 pr-5">
                                                {{ Helpers_set_symbol(($order['tax_type'] == 'included' ? $order['item_total'] : $order['item_total']+$order['total_tax_amount']) - $order['total_discount'] - $order['coupon_amount']) }}
                                                <hr>
                                            </dd>
                                            @if ($order->partial_payment != null)
                                                @php($partial_payment = json_decode($order->partial_payment,true))
                                                <dt class="col-6 text-left">
                                                    <div class="ml-auto max-w-130px">
                                                        <span>{{translate('Paid By')}} ({{'Wallet'}})</span>
                                                        <span>:</span>
                                                    </div>
                                                </dt>
                                                <dd class="col-6 col-xl-5 pr-5">
                                                    {{ Helpers_set_symbol($partial_payment['wallet_applied']) }}
                                                </dd>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>


            <div class="col-lg-4 order-print-area-right">
                <div class="card">
                    <div class="card-header border-0 pb-0 justify-content-center">
                        <h4 class="card-title">{{translate('Order Setup')}}</h4>
                    </div>

                    <div class="card-body">
                        <div class="hs-unfold w-100">
                            <span class="d-block form-label font-bold mb-2">
                                {{translate('Accept or reject order?')}}:
                            </span>
                            <div class="dropdown">
                                <button type="button" class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{translate('Make Action')}}
                                </button>
                                <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item manage-status" href="{{ route('admin.orders.approval.request.action',['id'=>$order['id'],'status'=>'accept']) }}" data-order_status="accept">
                                        {{ translate('accept') }}
                                    </a>
                                    <a class="dropdown-item manage-status" href="{{ route('admin.orders.approval.request.action',['id'=>$order['id'],'status'=>'reject']) }}" data-order_status="reject">
                                        {{ translate('reject') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="hs-unfold w-100 mt-3">
                            <span class="d-block form-label font-bold mb-2">{{translate('Payment Status')}}:</span>
                            <div class="dropdown">
                                <button class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    {{translate($order['payment_status'])}}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item change-payment-status" data-status="paid" data-route="{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'paid'])}}">{{ translate('paid') }}</a>
                                    <a class="dropdown-item change-payment-status" data-status="unpaid" data-route="{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'unpaid'])}}">{{ translate('unpaid') }}</a>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card-body">
                        <h5 class="form-label mb-3">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                {{translate('Customer information')}}
                            </span>
                        </h5>
                        @if($order->is_guest == 1)
                            <div class="media align-items-center deco-none customer--information-single">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img" src="{{asset('assets/admin/img/admin.jpg')}}" alt="{{ translate('Image Description')}}">
                                </div>
                                <div class="media-body">
                                    <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                        {{translate('Guest Customer')}}
                                    </span>
                                </div>
                            </div>
                        @else
                            @if($order->user_id == null)
                                <div class="media align-items-center deco-none customer--information-single">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img" src="{{asset('assets/admin/img/admin.jpg')}}" alt="{{ translate('Image Description')}}">
                                    </div>
                                    <div class="media-body">
                                        <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                            {{translate('Walking Customer')}}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @if($order->user_id != null && !isset($order->customer) )
                                <div class="media align-items-center deco-none customer--information-single">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img" src="{{asset('assets/admin/img/admin.jpg')}}" alt="{{ translate('Image Description')}}">
                                    </div>
                                    <div class="media-body">
                                        <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                            {{translate('Customer_not_available')}}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            @if(isset($order->customer) )
                                <div class="media align-items-center deco-none customer--information-single">
                                    <div class="avatar avatar-circle">
                                        <img class="avatar-img" src="{{asset($order->customer->image)}}" alt="{{ translate('Image Description')}}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                                    </div>
                                    <div class="media-body">
                                        <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                            <a href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                        </span>
                                        <span>
                                            {{\App\Models\Order::where('user_id',$order['user_id'])->count()}} {{translate("orders")}}
                                        </span>
                                        <span class="text--title font-semibold d-block">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a href="Tel:{{$order->customer['phone']}}">
                                                {{$order->customer['number']}}
                                            </a>
                                        </span>
                                        <span class="text--title">
                                            <i class="tio-email mr-2"></i>
                                            <a href="mailto:{{$order->customer['email']}}">
                                                {{$order->customer['email']}}
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                
                @if($order['order_type'] == 'service')
                    <div class="mt-3">
                        <span class="d-block form-label mb-2 font-bold">{{translate('Delivery Date & Time')}}:</span>
                        <div class="d-flex flex-wrap g-2">
                            <div class="hs-unfold w-0 flex-grow min-w-160px">
                                <label class="input-date">
                                    <input class="js-flatpickr form-control flatpickr-custom min-h-45px form-control" type="text" value="{{ $order['delivery_date'] != null ? date('d M Y',strtotime($order['delivery_date'])) : 'dd-mm-yyy' }}" name="deliveryDate" id="deliveryDate" data-id="{{ $order['id'] }}" required disabled>
                                </label>
                            </div>
                            <div class="hs-unfold w-0 flex-grow min-w-160px">
                                <select class="custom-select time_slote" name="timeSlot" data-id="{{$order['id']}}" id="servicetimeSlot" readonly>
                                    <option disabled selected>{{translate('select')}} {{translate('Time Slot')}}</option>
                                    @foreach(\App\Models\ServiceTimeSlot::all() as $timeSlot)
                                        <option value="{{$timeSlot['id']}}" {{$timeSlot->id == $order->delivery_timeslot_id ?'selected':''}}>
                                            {{date('H:i A', strtotime($timeSlot['time']))}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif

                @if($order['order_type'] != 'amc')
                    <div class="card mt-2">
                        <div class="card-body">
                            @php($address=\App\Models\CustomerAddresses::find($order['delivery_address_id']))

                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title">
                                    <span class="card-header-icon">
                                        <i class="tio-user"></i>
                                    </span>
                                    <span>
                                        {{translate('delivery information')}}
                                    </span>
                                </h5>
                            </div>

                            @if(isset($address))
                                <div class="delivery--information-single flex-column mt-3">
                                    <div class="d-flex">
                                        <span class="name">
                                            {{translate('name')}}
                                        </span>
                                        <span class="info">
                                            {{$address['contact_person_name']}}
                                        </span>
                                    </div>
                                    <div class="d-flex">
                                        <span class="name">
                                            {{translate('phone')}}
                                        </span>
                                        <span class="info">
                                            {{ $address['contact_person_number']}}
                                        </span>
                                    </div>
                                    @if($address['house_road'])
                                        <div class="d-flex">
                                            <span class="name">
                                                {{translate('house')}}
                                            </span>
                                            <span class="info">
                                                #{{ $address['house_road']}}
                                            </span>
                                        </div>
                                    @endif
                                    @if($address['address1'])
                                        <div class="d-flex">
                                            <span class="name">{{translate('address')}}</span>
                                            <span class="info">#{{ $address['address1']}}</span>
                                        </div>
                                    @endif
                                    @if($address['address2'])
                                        <div class="d-flex">
                                            <span class="name">{{translate('address')}}</span>
                                            <span class="info">#{{ $address['address2']}}</span>
                                        </div>
                                    @endif
                                    @if($address['city'])
                                        <div class="d-flex">
                                            <span class="name">{{translate('city')}}</span>
                                            <span class="info">#{{ $address['city']}}</span>
                                        </div>
                                    @endif
                                    <hr class="w-100">
                                    <div>
                                        <a target="_blank"
                                            href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                            <i class="tio-poi"></i> {{$address['city']}}
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/flatpicker.js') }}"></script>

    <script>
        "use strict";
        
        $('.manage-status').on('click', function(event) {
            event.preventDefault();
            var status = $(this).data('status');
            var date = $('#from_date').val();
            var order_status = $(this).data('order_status');
            var confirmMessage = '{{ translate("You Want to ") }}' + order_status + '{{ translate(" this order") }}?';

            console.log(date);
            Swal.fire({
                title: '{{translate("Are you sure?")}}',
                text: confirmMessage,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#01684b',
                cancelButtonText: '{{translate("No")}}',
                confirmButtonText: '{{translate("Yes")}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) 
                {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.orders.approval.request.action', ['id' => $order['id']]) }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            delivery_date : date,
                            timeSlot : $('#timeSlot').val(),
                            status : order_status,
                        },
                        success: function (data) {
                            if (data.status == true) {
                                location.href = "{{ route('admin.orders.approval_request') }}";
                            }
                        },
                    });
                }
            })
        });

        $('.change-payment-status').on('click', function(event) {
            event.preventDefault();
            var status = $(this).data('status');
            var message = '{{ translate("Change status to") }} ' + status + ' ?';
            var route = $(this).data('route');
            console.log(status);
            console.log(message);
            console.log(route);
            route_alert(route, message);
        });

        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush
