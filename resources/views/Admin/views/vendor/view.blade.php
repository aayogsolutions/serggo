@extends('Admin.layouts.app')

@section('title', translate('Vendor Details'))

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="page-header border-bottom">
                <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/employee.png')}}" class="w--20" alt="{{ translate('vendor') }}">
                </span>
                    <span class="page-header-title pt-2">
                        {{translate('Vendor_Details')}}
                    </span>
                </h1>
            </div>
        </div>

        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-auto mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('vendor')}} {{translate('id')}} #{{$vendor['id']}}</h1>
                    <span class="d-block">
                        <i class="tio-date-range"></i> {{translate('joined_at')}} : {{date('d M Y '.config('timeformat'),strtotime($vendor['created_at']))}}
                    </span>
                </div>

                <div class="col-auto ml-auto">
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle mr-1"
                       href="{{route('admin.vendor.view',[$vendor['id']-1])}}"
                       data-toggle="tooltip" data-placement="top" title="{{ translate('Previous vendor') }}">
                        <i class="tio-arrow-backward"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-soft-secondary rounded-circle"
                       href="{{route('admin.vendor.view',[$vendor['id']+1])}}" data-toggle="tooltip"
                       data-placement="top" title="{{ translate('Next vendor') }}">
                        <i class="tio-arrow-forward"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="row mb-2 g-2">


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card bg--2">
                    <img class="resturant-icon" src="{{asset('assets/admin/img/dashboard/1.png')}}" alt="{{ translate('image') }}">
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">{{translate('wallet')}} {{translate('balance')}}</div>
                    <div class="for-card-count">{{ Helpers_set_symbol($vendor->wallet_balance??0)}}</div>
                </div>
            </div>


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card bg--3">
                    <img class="resturant-icon" src="{{asset('assets/admin/img/dashboard/3.png')}}" alt="{{ translate('image') }}">
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">{{translate('loyalty_point')}} {{translate('balance')}}</div>
                    <div class="for-card-count">{{$vendor->loyalty_point??0}}</div>
                </div>
            </div>
        </div>


        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="card">
                    <div class="card-header">
                        <div class="card--header">
                        <h5 class="card-title">{{ translate('Order List') }} <span class="badge badge-soft-secondary">{{ count($orders) }}</span></h5>
                            <form action="{{url()->current()}}" method="GET">
                                <div class="input-group">
                                    <input id="datatableSearch_" type="search" name="search"
                                           class="form-control"
                                           placeholder="{{translate('Search by Order Id or Order Amount')}}" aria-label="Search"
                                           value="{{$search}}" required autocomplete="off">
                                    <div class="input-group-append">
                                        <button type="submit" class="input-group-text">
                                            {{__('Search')}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <h5 class="card-header-title">
                        </h5>
                    </div>
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                            <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th class="text-center">{{translate('order')}} {{translate('id')}}</th>
                                <th class="text-center">{{translate('total amount')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($orders as $key=>$order)
                                <tr>
                                    <td>{{$orders->firstItem()+$key}}</td>
                                    <td class=" text-center">
                                        <a href="{{route('admin.orders.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                                    </td>
                                    <td class="text-center">{{ Helpers_set_symbol($order['order_amount']) }}</td>
                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="action-btn"
                                                href="{{route('admin.orders.details',['id'=>$order['id']])}}"><i
                                                    class="tio-invisible"></i></a>
                                            <a class="action-btn btn--primary btn-outline-primary" target="_blank"
                                                href="{{route('admin.orders.generate-invoice',[$order['id']])}}">
                                                <i class="tio-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer">
                        {!! $orders->links('pagination::bootstrap-4') !!}
                        </div>
                        @if(count($orders)==0)
                            <div class="text-center p-4">
                                <img class="w-120px mb-3" src="{{asset('assets/admin')}}/svg/illustrations/sorry.svg" alt="{{ translate('image') }}">
                                <p class="mb-0">{{ translate('No_data_to_show')}}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>



            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                @if($vendor)
                                    {{$vendor['name']}}
                                    @else
                                    {{ translate('vendor') }}
                                @endif
                            </span>
                        </h4>
                    </div>

                    @if($vendor)
                        <div class="card-body">
                            <div class="media align-items-center customer--information-single" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img
                                        class="avatar-img"
                                        src="{{$vendor->imageFullPath}}"
                                        alt="{{ translate('vendor') }}">
                                </div>
                                <div class="media-body">
                                    <ul class="list-unstyled m-0">
                                        <li class="pb-1">
                                            <i class="tio-email mr-2"></i>
                                            <a href="mailto:{{$vendor['email']}}">{{$vendor['email']}}</a>
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a href="Tel:{{$vendor['phone']}}">{{$vendor['phone']}}</a>
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-shopping-basket-outlined mr-2"></i>
                                            {{$vendor->vendororders->count()}} {{translate('orders')}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>{{translate('contact')}} {{translate('info')}}</h5>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
