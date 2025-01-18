@extends('Admin.layouts.app')

@section('title', translate('Serice Men List'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/employee.png')}}" class="w--20" alt="{{ translate('service_mens') }}">
            </span>
            <span>
                {{translate('Service Men list')}} <span
                    class="badge badge-soft-primary ml-2 badge-pill">{{ $service_mens->total() }}</span>
            </span>
        </h1>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card--header">
                <form action="{{url()->current()}}" method="GET">
                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                            placeholder="{{translate('Search by Name or Phone or Email')}}" aria-label="Search"
                            value="{{$search}}" required autocomplete="off">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text">
                                {{ translate('search') }}
                            </button>
                        </div>
                    </div>
                </form>

                <div class="hs-unfold ml-auto">
                    <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40"
                        href="javascript:;" data-hs-unfold-options='{
                            "target": "#usersExportDropdown",
                            "type": "css-animation"
                            }'>
                        <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                    </a>

                    <div id="usersExportDropdown"
                        class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                        <span class="dropdown-header">{{ translate('download') }}
                            {{ translate('options') }}</span>
                        <a id="export-excel" class="dropdown-item"
                            href="{{route('admin.customer.export', ['search'=>Request::get('search')])}}">
                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                src="{{ asset('assets/admin') }}/svg/components/excel.svg"
                                alt="{{ translate('excel') }}">
                            {{ translate('excel') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-hover table-align-middle m-0 text-14px">
                <thead class="thead-light">
                    <tr class="word-nobreak">
                        <th>
                            {{translate('#')}}
                        </th>
                        <th class="table-column-pl-0">{{translate('Service_men name')}}</th>
                        <th class="text-center">{{translate('Contact Info')}}</th>
                        <th class="text-center">{{translate('Wallet balance')}}</th>
                        <th class="text-center">{{translate('Total Orders')}}</th>
                        <th class="text-center">{{translate('Total Order Amount')}}</th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                </thead>
                <tbody id="set-rows">
                    @foreach($service_mens as $key=>$service_men)
                    <tr>
                        <td>
                            {{$service_mens->firstItem()+$key}}
                        </td>
                        <td class="table-column-pl-0">
                            <a href="#" class="product-list-media">
                                <img class="rounded-full" src="{{ asset($service_men->image)}}"
                                    alt="{{ translate('service_men') }}"
                                    onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                                <div class="table--media-body">
                                    <h5 class="title m-0">
                                        {{$service_men['name']}}
                                    </h5>
                                </div>
                            </a>
                        </td>
                        <td class="text-center">
                            <h5 class="m-0 ">
                                <a href="mailto:{{$service_men['email']}}">{{$service_men['email']}}</a>
                            </h5>
                            <div>
                                <a href="Tel:{{$service_men['phone']}}">{{$service_men['number']}}</a>
                            </div>
                        </td>
                        <td class="text-center">
                            {{$service_men['wallet_balance']}}
                        </td>
                        <td>
                            <div class="text-center">
                                <a href="#">
                                    <span class="badge badge-soft-info py-2 px-3 font-medium">
                                        <h5>total order</h5>
                                    </span>
                                </a>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">

                                {{ Helpers_set_symbol(\App\Models\Vendor::total_order_amount($service_men->id)) }}

                            </div>
                        </td>
                        <td>
                            <label class="toggle-switch my-0">
                                <input type="checkbox" class="toggle-switch-input status-change-alert"
                                    id="stocksCheckbox{{ $service_men->id }}"
                                    data-route="{{ route('admin.service_men.status', [$service_men->id, $service_men->is_block == 1 ? 0 : 1]) }}"
                                    data-message="{{ $service_men->is_block? translate('you_want_to_change_the_status_for_this_customer'): translate('you_want_to_change_the_status_for_this_customer') }}"
                                    {{ $service_men->is_block == 0? 'checked' : '' }}>
                                <span class="toggle-switch-label mx-auto text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td class="btn--container justify-content-center">
                            <a class="action-btn" href="{{route('admin.service_men.view',[$service_men['id']])}}">
                                <i class="tio-invisible"></i>
                            </a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($service_mens) == 0)
        <div class="text-center p-4">
            <img class="w-120px mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}"
                alt="{{ translate('image') }}">
            <p class="mb-0">{{translate('No_data_to_show')}}</p>
        </div>
        @endif

        <div class="card-footer">
            {!! $service_mens->links('pagination::bootstrap-4') !!}
        </div>

    </div>
</div>
@endsection