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
                  
                        {{ translate(ucwords(str_replace('_',' ','Pending' ))) }} {{translate('Orders')}}
                   
                    <span class="badge badge-pill badge-soft-secondary ml-2">{{ $orders->total() }}</span>
                </span>

            </h1>
        </div>

        <div class="card">
            

            

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
                        
                    </div>
                </div>

                <div class="table-responsive datatable-custom">
                    <table class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>
                                    {{translate('SN')}}
                                </th>
                                <th class="table-column-pl-0">
                                    {{translate('order ID')}}
                                </th>
                                <th class="table-column-pl-0">
                                    {{translate('Customer')}}
                                </th>
                                <th class="table-column-pl-0">
                                    {{translate('Belong_to')}}
                                </th>
                                <th class="table-column-pl-0">
                                    {{translate('Number_of_products')}}
                                </th>
                                <th>
                                    {{translate('Total amount')}}
                                </th>
                                <th>
                                    {{translate('delivered_by')}}
                                </th>                          
                                <th>
                                    <div class="text-center">
                                        {{translate('delivered_address')}}
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
                                <td>
                                    <div>
                                        <a class="text-body text-capitalize font-medium"
                                            href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['name']}}</a>
                                    </div>
                                    <div class="text-sm">
                                        <a href="Tel:{{$order->customer['number']}}">{{$order->customer['number']}}</a>
                                    </div>
                                </td>
                                <td>
                                    @if($order->vender_id == null)
                                        <span class="text-success">
                                            {{translate('Admin')}}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <div>
                                                <a class="text-body text-capitalize font-medium"
                                                    href="{{route('admin.vendor.view',[$order['user_id']])}}">{{translate($order->vendororders->name)}}</a>
                                            </div>
                                            <div class="text-sm">
                                                <a href="javascript:void(0);">{{$order->vendororders->number}}</a>
                                            </div>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-success">
                                        {{ $order->OrderDetails->count() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="mw-90">
                                        <div>
                                            {{ Helpers_set_symbol($order['order_amount']) }}
                                        </div>
                                        @if($order->payment_status == 'paid')
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
                                <td>
                                    @if($order->delivered_by == 0)
                                        <span class="text-success">
                                            {{translate('Admin')}}
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <div>
                                                <a class="text-body text-capitalize font-medium"
                                                    href="{{route('admin.vendor.view',[$order['user_id']])}}">{{translate($order->vendororders->name)}}</a>
                                            </div>
                                            <div class="text-sm">
                                                <a href="javascript:void(0);">{{$order->vendororders->number}}</a>
                                            </div>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-capitalize text-center">
                                    @php($address = json_decode($order['delivery_address'],true))
                                    @if($address != null)
                                        {{translate($address['house_road'].', '.$address['address1'])}}
                                        <br>
                                        {{translate($address['address2'].', '.$address['city'])}}
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn btn--primary btn-outline-primary" href="{{route('admin.orders.approval.request.view',['id'=>$order['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>

                                        <!-- <a class="action-btn btn-outline-primary-2" target="_blank" href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                            <i class="tio-print"></i>
                                        </a> -->
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
