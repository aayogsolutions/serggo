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

                            <h6 class="mt-5">
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
                            </h6>
                        </div>
                        <div class="order-invoice-right mt-3 mt-sm-0">
                            <div class="btn--container ml-auto align-items-center justify-content-end">
                                <a class="btn btn--info print--btn" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                    <i class="tio-print mr-1"></i> 
                                    {{translate('print')}} {{translate('invoice')}}
                                </a>
                            </div>
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
                                    <span class="text-body mr-2">
                                        {{ translate('payment') }} {{ translate('status') }} : 
                                    </span>

                                    @if($order['payment_status'] == 'paid')
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
                                        {{ translate('order') }} {{ translate('type') }}:
                                    </span>
                                    <label class="badge badge-soft-primary ml-3">
                                        {{ translate(str_replace('_',' ',$order['order_type'])) }}
                                    </label>
                                </h6>
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
                        @php($vatStatus = '')
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{translate('SL')}}</th>
                                        @if($order['editable']== 1 || $order['editable']== 3)
                                            <th class="border-0">{{translate('Replace')}}</th>
                                        @endif
                                        <th class="border-0">{{translate('Item details')}}</th>
                                        <th class="border-0 text-right">{{translate('Price')}}</th>
                                        <th class="border-0 text-right">{{translate('Discount')}}</th>
                                        <th class="text-right border-0">{{translate('Total Price')}}</th>
                                    </tr>
                                </thead>
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
                                                                <h5 class='line--limit-1' title="{{$pro_d['name']}}">{{$pro_d['name']}}</h5>
                                                                <h5 class='mt-1'><span class='text-body'>{{translate('Unit')}}</span>: {{$pro_d['unit']}} </h5>
                                                                <h5 class='mt-1'><span class='text-body'>{{translate('Unit Price')}}</span>: {{$pro_d['price']}} </h5>
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
                                                        <h5 class="mt-1"><span class="text-body">{{translate('Unit')}}</span> : {{$detail['unit']}} </h5>
                                                        <h5 class="mt-1"><span class="text-body">{{translate('Unit Price')}}</span> : {{$detail['price']}} </h5>
                                                        <h5 class="mt-1"><span class="text-body">{{translate('QTY')}}</span> : {{$detail['quantity']}} </h5>
                                                    </div>
                                                </div>
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
                                                @php($updatedTotalTax+= $detail['vat_status'] === 'included' ? 0 : $detail['tax_amount']*$detail['quantity'])
                                                @php($vatStatus = $detail['vat_status'])
                                                @php($totalItemDiscount += $detail['discount_on_product'] * $detail['quantity'])
                                                @php($price_after_discount+=$amount-$totalItemDiscount)
                                                @php($subTotal+=$price_after_discount)
                                                <h5>{{ Helpers_set_symbol(($detail['price'] * $detail['quantity']) - ($detail['discount_on_product'] * $detail['quantity'])) }}</h5>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                <tr>
                                    <td colspan="12" class="td-p-0">
                                        <hr class="m-0" >
                                    </td>
                                </tr>
                            </table>
                        </div>

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
                                            {{translate('GST')}} {{ $vatStatus == 'included' ? translate('(included)') : '' }}:
                                        </div>
                                    </dt>
                                    <dd class="col-6 col-xl-5 pr-5">
                                        {{ Helpers_set_symbol($totalTax) }}
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
                                        <hr>
                                    </dd>

                                    <dt class="col-6 text-left">
                                        <div class="ml-auto max-w-130px">
                                            {{translate('total')}}:
                                        </div>
                                    </dt>
                                    <dd class="col-6 col-xl-5 pr-5">
                                        {{ Helpers_set_symbol($total+$del_c+$updatedTotalTax-$order['coupon_discount_amount']-$order['extra_discount']) }}
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
                                            {{ Helpers_set_symbol($due_amount) }}
                                        </dd>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-4 order-print-area-right">
                @if($order['order_type'] != 'pos')
                    <div class="card">
                        <div class="card-header border-0 pb-0 justify-content-center">
                            <h4 class="card-title">{{translate('Order Setup')}}</h4>
                        </div>

                        @if(isset($order->offline_payment))
                            <div class="card mt-3">
                                <div class="card-body text-center">
                                    @if($order->offline_payment?->status == 1)
                                        <h4 class="">{{ translate('Payment_verified') }}</h4>
                                    @else
                                        <h4 class="">{{ translate('Payment_verification') }}</h4>
                                        <p class="text-danger">{{ translate('please verify the payment before confirm order') }}</p>
                                        <div class="mt-3">
                                            <button class="btn btn--primary" type="button" id="verifyPaymentButton" data-id="{{ $order['id'] }}"
                                                    data-target="#payment_verify_modal" data-toggle="modal">{{ translate('Verify_Payment') }}</button>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        @endif

                        <div class="card-body">
                            @if($order['order_type'] != 'pos')
                            <div class="hs-unfold w-100">
                                <span class="d-block form-label font-bold mb-2">{{translate('Change Order Status')}}:</span>
                                <div class="dropdown">
                                    <button class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        {{$order['order_status'] == 'processing' ? translate('packaging') : translate($order['order_status'])}}
                                    </button>
                                    <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="pending">{{ translate('pending') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="confirmed">{{ translate('confirmed') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="packaging">{{ translate('packaging') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="out_for_delivery">{{ translate('out_for_delivery') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="delivered">{{ translate('delivered') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="returned">{{ translate('returned') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="failed">{{ translate('failed') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="canceled">{{ translate('canceled') }}</a>
                                        <a class="dropdown-item manage-status" href="javascript:void(0);" data-order_status="rejected">{{ translate('rejected') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="hs-unfold w-100 mt-3">
                                <span class="d-block form-label font-bold mb-2">{{translate('Payment Status')}}:</span>
                                <div class="dropdown">
                                    <button class="form-control h--45px dropdown-toggle d-flex justify-content-between align-items-center w-100" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        {{translate($order['payment_status'])}}
                                    </button>
                                    @if($order['payment_method'] == 'offline_payment' && $order->offline_payment?->status != 1)
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item offline-payment" data-message="{{ translate('You can not change status of unverified offline payment') }}"
                                            data-status="paid" href="#">{{ translate('paid') }}</a>
                                            <a class="dropdown-item offline-payment" data-message="{{ translate('You can not change status of unverified offline payment') }}"
                                            data-status="unpaid" href="#">{{ translate('unpaid') }}</a>
                                        </div>
                                    @else
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item change-payment-status" data-status="paid" data-route="{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'paid'])}}">{{ translate('paid') }}</a>
                                            <a class="dropdown-item change-payment-status" data-status="unpaid" data-route="{{route('admin.orders.payment-status',['id'=>$order['id'],'payment_status'=>'unpaid'])}}">{{ translate('unpaid') }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-3">
                                <span class="d-block form-label mb-2 font-bold">{{translate('Delivery Date & Time')}}:</span>
                                <div class="d-flex flex-wrap g-2">
                                    <div class="hs-unfold w-0 flex-grow min-w-160px">
                                        <label class="input-date">
                                            <input class="js-flatpickr form-control flatpickr-custom min-h-45px form-control" type="text" value="{{ date('d M Y',strtotime($order['delivery_date'])) }}"
                                                name="deliveryDate" id="from_date" data-id="{{ $order['id'] }}" required>
                                        </label>
                                    </div>
                                    <div class="hs-unfold w-0 flex-grow min-w-160px">
                                        <select class="custom-select custom-select time_slote" name="timeSlot" data-id="{{$order['id']}}">
                                            <option disabled selected>{{translate('select')}} {{translate('Time Slot')}}</option>
                                            @foreach(\App\Models\TimeSlot::all() as $timeSlot)
                                                <option value="{{$timeSlot['id']}}" {{$timeSlot->id == $order->time_slot_id ?'selected':''}}>{{date(config('time_format'), strtotime($timeSlot['start_time']))}}
                                                    - {{date(config('time_format'), strtotime($timeSlot['end_time']))}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if (!$order->delivery_man)
                                <div class="mt-3">
                                    <button class="btn btn--primary w-100" type="button" data-target="#assign_delivey_man_modal" data-toggle="modal">{{ translate('assign delivery man manually') }}</button>
                                </div>
                            @endif
                            @if ($order->delivery_man)
                                <div class="card mt-2">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3 d-flex flex-wrap align-items-center">
                                            <span class="card-header-icon">
                                                <i class="tio-user"></i>
                                            </span>
                                            <span>{{ translate('deliveryman') }}</span>
                                            @if ($order->order_status != 'delivered')
                                                <a type="button" href="#assign_delivey_man_modal" class="text--base cursor-pointer ml-auto text-sm"
                                                    data-toggle="modal" data-target="#assign_delivey_man_modal">
                                                    {{ translate('change') }}
                                                </a>
                                            @endif
                                        </h5>
                                        <div class="media align-items-center deco-none customer--information-single">

                                            <div class="avatar avatar-circle">
                                                <img class="avatar-img"
                                                        src="{{$order->delivery_man->imageFullPath }}"
                                                        alt="{{ translate('Image Description')}}">
                                            </div>
                                            <div class="media-body">
                                                <a href="{{ route('admin.delivery-man.preview', [$order->delivery_man['id']]) }}">
                                                    <span class="text-body d-block text-hover-primary mb-1">{{ $order->delivery_man['f_name'] . ' ' . $order->delivery_man['l_name'] }}</span>
                                                </a>

                                                <span class="text--title font-semibold d-flex align-items-center">
                                                <i class="tio-shopping-basket-outlined mr-2"></i>
                                                {{\App\Models\Order::where(['delivery_man_id' => $order['delivery_man_id'], 'order_status' => 'delivered'])->count()}} {{ translate('orders_delivered') }}
                                                </span>
                                                <span class="text--title font-semibold d-flex align-items-center">
                                                    <i class="tio-call-talking-quiet mr-2"></i>
                                                    <a href="Tel:{{ $order->delivery_man['phone'] }}">{{ $order->delivery_man['phone'] }}</a>
                                                </span>
                                                <span class="text--title font-semibold d-flex align-items-center">
                                                    <i class="tio-email-outlined mr-2"></i>
                                                    <a href="mailto:{{$order->delivery_man['email']}}">{{$order->delivery_man['email']}}</a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                @endif

                <div class="card mt-2">
                    <div class="card-body">
                        <h5 class="form-label mb-3">
                        <span class="card-header-icon">
                        <i class="tio-user"></i>
                        </span>
                            <span>{{translate('Customer information')}}</span>
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
                                        <img class="avatar-img" src="{{asset($order->customer->image)}}" alt="{{ translate('Image Description')}}">
                                    </div>
                                    <div class="media-body">
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    <a href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                </span>
                                        <span>{{\App\Models\Order::where('user_id',$order['user_id'])->count()}} {{translate("orders")}}</span>
                                        <span class="text--title font-semibold d-block">
                            <i class="tio-call-talking-quiet mr-2"></i>
                            <a href="Tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                </span>
                                        <span class="text--title">
                            <i class="tio-email mr-2"></i>
                            <a href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                        </span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="shipping-address-modal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-top-cover bg-dark text-center">
                    <figure class="position-absolute right-0 bottom-0 left-0">
                        <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                             viewBox="0 0 1920 100.1">
                            <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z"/>
                        </svg>
                    </figure>

                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                                aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor"
                                      d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="modal-top-cover-icon">
                    <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                      <i class="tio-location-search"></i>
                    </span>
                </div>

                @php($address=\App\Models\CustomerAddresses::find($order['delivery_address_id']))
                @if(isset($address))
                    <form action="{{route('admin.order.update-shipping',[$order['delivery_address_id']])}}"
                          method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('type')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address_type"
                                           value="{{$address['address_type']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('contact')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_number"
                                           value="{{$address['contact_person_number']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('name')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_name"
                                           value="{{$address['contact_person_name']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('address')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address" value="{{$address['address']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('road')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="road" value="{{$address['road']}}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('house')}}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="house" value="{{$address['house']}}">
                                </div>
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('floor')}}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="floor" value="{{$address['floor']}}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('latitude')}}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="latitude"
                                           value="{{$address['latitude']}}"
                                           required>
                                </div>
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{translate('longitude')}}
                                </label>
                                <div class="col-md-4 js-form-message">
                                    <input type="text" class="form-control" name="longitude"
                                           value="{{$address['longitude']}}" required>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white"
                                    data-dismiss="modal">{{translate('close')}}</button>
                            <button type="submit"
                                    class="btn btn-primary">{{translate('save')}} {{translate('changes')}}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>


    <div class="modal fade" id="assign_delivey_man_modal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{translate('Assign Delivery Man')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0)" method="post" id="assign_service_man">
                        <div class="row">
                            <div class="col-md-12 my-2">
                                <select name="service_man" class="form-control" id="service_man">
                                    <option selected disabled>Select Service Man</option>
                                    @foreach($servicemanlist as $service_man)
                                        <option value="{{$service_man->id}}">{{$service_man->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12 my-2">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>

    @if($order->offline_payment)
        <div class="modal fade" id="payment_verify_modal">
            <div class="modal-dialog modal-lg offline-details">
                <div class="modal-content">
                    <div class="modal-header justify-content-center">
                        <h4 class="modal-title pb-2">{{translate('Payment_Verification')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    </div>
                    <div class="card">
                        <div class="modal-body mx-2">
                            <p class="text-danger">{{translate('Please Check & Verify the payment information whether it is correct or not before confirm the order.')}}</p>
                            <h5>{{translate('customer_Information')}}</h5>

                            <div class="card-body">
                                @if($order->is_guest == 0)
                                    <p>{{ translate('name') }} : {{ $order->customer ? $order->customer->f_name.' '. $order->customer->l_name: ''}} </p>
                                    <p>{{ translate('contact') }} : {{ $order->customer ? $order->customer->phone: ''}}</p>
                                @else
                                    <p>{{ translate('guest_customer') }} </p>
                                @endif
                            </div>

                            <h5>{{translate('Payment_Information')}}</h5>
                            @php($payment = json_decode($order->offline_payment?->payment_info, true))
                            <div class="row card-body">
                                <div class="col-md-6">
                                    <p>{{ translate('Payment_Method') }} : {{ $payment['payment_name'] }}</p>
                                    @foreach($payment['method_fields'] as $fields)
                                        @foreach($fields as $fieldKey => $field)
                                            <p>{{ $fieldKey }} : {{ $field }}</p>
                                        @endforeach
                                    @endforeach
                                </div>
                                <div class="col-md-6">
                                    <p>{{ translate('payment_note') }} : {{ $payment['payment_note'] }}</p>
                                    @foreach($payment['method_information'] as $infos)
                                        @foreach($infos as $infoKey => $info)
                                            <p>{{ $infoKey }} : {{ $info }}</p>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end my-2 mx-3">
                        <a type="reset" class="btn btn--reset verify-offline-payment" data-status="2">{{ translate('Payment_Did_Not_Received') }}</a>
                        <a type="submit" class="btn btn--primary verify-offline-payment" data-status="1">{{ translate('Yes,_Payment_Received') }}</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/flatpicker.js') }}"></script>

    <script>
        "use strict";

        $('.manage-status').on('click', function(event) {
            event.preventDefault();
            
            var order_status = $(this).data('order_status');
            var confirmMessage = '{{ translate("You Want to ") }}' + order_status + '{{ translate(" this order") }}?';
            
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
                        url: "{{ route('admin.orders.status', ['id' => $order['id']]) }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            delivery_date : $('#from_date').val(),
                            timeSlot : $('#timeSlot').val(),
                            order_status : order_status,
                        },
                        success: function (data) {
                            if (data.status == true) {
                                location.reload();
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

        $('.offline-payment').on('click', function(event) {
            event.preventDefault();
            var message = $(this).data('message');
            offline_payment_status_alert(message);
        });

        $('.assign-deliveryman').on('click', function(event) {
            event.preventDefault();
            var deliverymanId = $(this).data('deliveryman-id');
            addDeliveryMan(deliverymanId);
        });

        $('.verify-offline-payment').on('click', function(event) {
            event.preventDefault();
            var status = $(this).data('status');
            verify_offline_payment(status);
        });

        function offline_payment_order_alert(message) {
            Swal.fire({
                title: '{{translate("Payment_is_Not_Verified")}}',
                text: message,
                type: 'question',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: 'default',
                confirmButtonColor: '#01684b',
                cancelButtonText: '{{translate("Close")}}',
                confirmButtonText: '{{translate("Proceed")}}',
                reverseButtons: true
            }).then((result) => {

            })
        }

        function offline_payment_status_alert(message) {
            Swal.fire({
                title: '{{translate("Payment_is_Not_Verified")}}',
                text: message,
                type: 'question',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonColor: 'default',
                confirmButtonColor: '#01684b',
                cancelButtonText: '{{translate("Close")}}',
                confirmButtonText: '',
                reverseButtons: true
            }).then((result) => {

            })
        }

        function addDeliveryMan(id) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/add-delivery-man/{{$order['id']}}/' + id,
                data: $('#product_form').serialize(),
                success: function (data) {
                    //console.log(data);
                    location.reload();
                    if(data.status == true) {
                        toastr.success('{{ translate("Deliveryman successfully assigned/changed") }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else{
                        toastr.error('{{ translate("Deliveryman man can not assign/change in that status") }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }

                },
                error: function () {
                    toastr.error('Add valid data', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function verify_offline_payment(status) {
            $.ajax({
                type: "GET",
                url: '{{url('/')}}/admin/orders/verify-offline-payment/{{$order['id']}}/' + status,
                success: function (data) {
                    //console.log(data);
                    location.reload();
                    if(data.status == true) {
                        toastr.success('{{ translate("offline payment verify status changed") }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }else{
                        toastr.error('{{ translate("offline payment verify status not changed") }}', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }

                },
                error: function () {
                }
            });
        }

        function last_location_view() {
            toastr.warning('{{ translate("Only available when order is out for delivery!") }}', {
                CloseButton: true,
                ProgressBar: true
            });
        }

        $(document).on('ready', function () {
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

@endpush
