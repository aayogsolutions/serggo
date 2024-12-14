@extends('Admin.layouts.app')

@section('title', translate('Order List'))

@push('css_or_js')
    <style>
        table{
            width: 100%;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="mb-0 page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/all_orders.png')}}" class="w--20" alt="">
                </span>
                <span class="">
                    @if($status =='processing')
                        {{ translate(ucwords(str_replace('_',' ','Packaging' ))) }} {{translate('Orders')}}
                    @elseif($status =='failed')
                        {{ translate(ucwords(str_replace('_',' ','Failed to Deliver' ))) }} {{translate('Orders')}}
                    @else
                        {{ translate(ucwords(str_replace('_',' ',$status ))) }} {{translate('Orders')}}
                    @endif
                    <span class="badge badge-pill badge-soft-secondary ml-2">{{ $orders->total() }}</span>
                </span>

            </h1>
        </div>

        <div class="card">
            <div class="card-header shadow flex-wrap p-20px border-0">
                <h5 class="form-bold w-100 mb-3">{{ translate('Select Date Range') }}</h5>
                <form class="w-100">
                    <div class="row g-3 g-sm-4 g-md-3 g-lg-4">
                        
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-date-group">
                                <label class="input-label" for="start_date">{{ translate('Start Date') }}</label>
                                <label class="input-date">
                                    <input type="text" id="start_date" name="start_date" value="{{$startDate}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="input-date-group">
                                <label class="input-label" for="end_date">{{ translate('End Date') }}</label>
                                <label class="input-date">
                                    <input type="text" id="end_date" name="end_date" value="{{$endDate}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="yy-mm-dd" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-4 col-lg-2">
                            
                        </div>
                        <div class="col-sm-6 col-md-12 col-lg-4 __btn-row">
                            <a href="{{ route('admin.orders.list',['all']) }}" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                            <button type="submit" id="show_filter_data" class="btn w-100 btn--primary min-h-45px">{{translate('show data')}}</button>
                        </div>
                    </div>
                </form>
            </div>

            @if($status == 'all')
                <div class="p-20px pb-0 mt-4">
                    <div class="row g-3 g-sm-4 g-md-3 g-lg-4">

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['pending'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/pending.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('pending')}}</span>
                                        </h6>
                                        <span class="card-title text-0661CB">
                                            {{ $countData['pending'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['confirmed'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/confirmed.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('confirmed')}}</span>
                                        </h6>
                                        <span class="card-title text-107980">
                                            {{ $countData['confirmed'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['packaging'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/processing.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('packaging')}}</span>
                                        </h6>
                                        <span class="card-title text-danger">
                                            {{ $countData['packaging'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['out_for_delivery'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/out-for-delivery.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('out_for_delivery')}}</span>
                                        </h6>
                                        <span class="card-title text-00B2BE">
                                            {{ $countData['out_for_delivery'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['delivered'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/1.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('delivered')}}</span>
                                        </h6>
                                        <span class="card-title text-success">
                                            {{ $countData['delivered'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['all'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/2.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('Canceled')}}</span>
                                        </h6>
                                        <span class="card-title text-danger">
                                            {{ $countData['canceled'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['returned'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/3.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('returned')}}</span>
                                        </h6>
                                        <span class="card-title text-warning">
                                            {{ $countData['returned'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <a class="order--card h-100" href="{{route('admin.orders.list',['failed'])}}">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                            <img src="{{asset('assets/admin/img/delivery/4.png')}}" alt="{{ translate('dashboard')}}" class="oder--card-icon">
                                            <span>{{translate('failed_to_deliver')}}</span>
                                        </h6>
                                        <span class="card-title text-danger">
                                            {{ $countData['failed'] }}
                                        </span>
                                    </div>
                                </a>
                            </div>
                    </div>
                </div>
            @endif

            <div class="card-body p-20px">
                <div class="order-top">
                    <div class="card--header">
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                       class="form-control"
                                       placeholder="{{translate('Ex : Search by ID, order or payment status')}}" aria-label="Search"
                                       value="{{$search}}" required autocomplete="off">
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text">
                                        {{translate('Search')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                data-hs-unfold-options='{
                                        "target": "#usersExportDropdown",
                                        "type": "css-animation"
                                    }'>
                                <i class="tio-download-to mr-1"></i> 
                                {{ translate('export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('download') }}
                                    {{ translate('options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.orders.export', [$status, 'branch_id'=>Request::get('branch_id'), 'start_date'=>Request::get('start_date'), 'end_date'=>Request::get('end_date'), 'search'=>Request::get('search')])}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('assets/admin') }}/svg/components/excel.svg"
                                        alt="{{ translate('Image Description')}}">
                                    {{ translate('excel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                        <tr>
                            <th>
                                {{translate('#')}}
                            </th>
                            <th class="table-column-pl-0">
                                {{translate('order ID')}}
                            </th>
                            <th>
                                <div class="text-center">
                                    {{translate('Order')}} {{translate('type')}}
                                </div>
                            </th>
                            <th>
                                <div class="text-center">
                                    {{translate('Blong')}} {{translate('to')}}
                                </div>
                            </th>
                            <th>{{translate('Delivery')}} {{translate('date')}}</th>
                            <th>{{translate('Time Slot')}}</th>
                            <th>{{translate('customer')}}</th>
                            <th>{{translate('total amount')}}</th>
                            <th>
                                <div class="text-center">
                                    {{translate('order')}} {{translate('status')}}
                                </div>
                            </th>
                            <th>
                                <div class="text-center">
                                    {{translate('order')}} {{translate('type')}}
                                </div>
                            </th>
                            <th>
                                <div class="text-center">
                                    {{translate('action')}}
                                </div>
                            </th>
                        </tr>
                        </thead>

                        <tbody id="set-rows">
                        @foreach($orders as $key=>$order)
                            <tr class="status-{{$order['order_status']}} class-all">
                                <td class="">
                                    {{$orders->firstItem()+$key}}
                                </td>
                                <td class="table-column-pl-0">
                                    <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                </td>
                                <td class="table-column-pl-0">
                                    <span class="badge badge-soft-info py-2 px-3">
                                        {{$order['order_type']}}
                                    </span>
                                </td>
                                <td class="table-column-pl-0">
                                    <span class="badge badge-soft-info py-2 px-3">
                                        {{$order->vender_id != null ? 'Vendor :- '.$order->vendororders->name : translate('Admin')}}
                                    </span>
                                </td>
                                <td>
                                    {{$order['delivery_date'] != null ? date('d M Y',strtotime($order['delivery_date'])) : 'Not Assigned'}}
                                </td>
                                <td>
                                    @if($order->order_type == 'goods')
                                        <span>
                                            {{$order->delivery_timeslot_id != null ? date('H:i A', strtotime($order->TimeSlot['start_time'])).' - ' .date('H:i A', strtotime($order->TimeSlot['end_time'])) : translate('No Time Slot')}}
                                        </span>
                                    @else
                                        <span>
                                            {{$order->delivery_timeslot_id != null ? date('H:i A', strtotime($order->ServiceTimeSlot['time'] ?? translate('No Time Slot'))) : translate('No Time Slot')}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($order->is_guest == 0)
                                        @if(isset($order->customer))
                                            <div>
                                                <a class="text-body text-capitalize font-medium"
                                                   href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['name']}}</a>
                                            </div>
                                            <div class="text-sm">
                                                <a href="Tel:{{$order->customer['number']}}">{{$order->customer['number']}}</a>
                                            </div>
                                        @elseif($order->user_id != null && !isset($order->customer))
                                            <label class="text-danger">
                                                {{translate('Customer_not_available')}}
                                            </label>
                                        @else
                                            <label class="text-success">
                                                {{translate('Walking Customer')}}
                                            </label>
                                        @endif
                                    @else
                                        <label class="text-success"> 
                                            {{translate('Guest Customer')}}
                                        </label>
                                    @endif

                                </td>

                                <td>
                                    <div class="mw-90">
                                        <div>
                                            <?php
                                                $vatStatus = $order->details ? $order->details[0]->gst_status : '';
                                                if($vatStatus == 'included'){
                                                    $orderAmount = $order['order_amount'] - $order['total_tax_amount'];
                                                }else{
                                                    $orderAmount = $order['order_amount'];
                                                }
                                            ?>
                                            {{ Helpers_set_symbol($orderAmount) }}
                                        </div>
                                        @if($order->payment_status=='paid')
                                            <span class="text-success">
                                                {{translate('paid')}}
                                            </span>
                                        @else
                                            <span class="text-danger">
                                                {{translate($order['payment_status'])}}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- <td class="text-capitalize text-center">
                                    @if($order['editable']== 0)
                                        <span class="badge badge-soft-success">
                                            {{translate('N/A')}}
                                        </span>
                                    @elseif($order['editable']== 1)
                                        <span class="badge badge-soft-warning">
                                            {{translate('Edit processing')}}
                                        </span>
                                    @elseif($order['editable']== 2)
                                        <span class="badge badge-soft-info">
                                            {{translate('Edit Accepted')}}
                                        </span>
                                    @endif
                                </td> -->
                                <td class="text-capitalize text-center">
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info">
                                            {{translate('pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info">
                                            {{translate('confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning">
                                            {{translate('packaging')}}
                                        </span>
                                    @elseif($order['order_status']=='out_for_delivery')
                                        <span class="badge badge-soft-warning">
                                            {{translate('out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success">
                                            {{translate('delivered')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                            {{ translate(str_replace('_',' ',$order['order_status'])) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-capitalize text-center">
                                    @if($order['order_type']=='take_away')
                                        <span class="badge badge-soft-info">
                                            {{translate('take_away')}}
                                        </span>
                                    @elseif($order['order_type']=='pos')
                                        <span class="badge badge-soft-info">
                                        {{translate('POS')}}
                                    </span>
                                    @else
                                        <span class="badge badge-soft-success">
                                        {{translate($order['order_type'])}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn btn--primary btn-outline-primary" href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i class="tio-invisible"></i></a>
                                        <a class="action-btn btn-outline-primary-2" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                            <i class="tio-print"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @if(count($orders)==0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">
                        <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                    </div>
                @endif
            </div>
            <div class="card-footer border-0">
                <div class="d-flex justify-content-center justify-content-sm-end">
                    {!! $orders->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/flatpicker.js') }}"></script>
@endpush
