@extends('Admin.layouts.app')

@section('title', translate('employee role'))

@push('css')
    <style>
        li{
            list-style-type: none;
        }
    </style>
@endpush
@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/employee.png')}}" class="w--24" alt="{{ translate('employee') }}">
            </span>
            <span>
                {{translate('Employee Role Setup')}}
            </span>
        </h1>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form id="submit-create-role" method="post" action="{{route('admin.custom-role.store')}}">
                @csrf
                <div class="max-w-500px">
                    <div class="form-group">
                        <label class="form-label">{{translate('role_name')}}</label>
                        <input type="text" name="name" class="form-control" id="name" aria-describedby="emailHelp" placeholder="{{translate('Ex')}} : {{translate('Store')}}" >
                    </div>
                </div>

                <div class="d-flex">
                    <h5 class="input-label m-0 text-capitalize">{{translate('module_permission')}} : </h5>
                    <div class="check-item pb-0 w-auto">
                        <input type="checkbox" id="select_all">
                        <label class="title-color mb-0 pl-2" for="select_all">{{ translate('select_all')}}</label>
                    </div>
                </div>

                <div class="check--item-wrapper">
                    <div class="row w-100">
                        <div class="col-md-4">
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="dashboard_management" value="dashboard_management" class="form-check-input module-permission" id="dashboard_management">
                                    <label class="form-check-label text-nowrap" for="dashboard_management">{{translate('dashboard_management')}}</label>
                                </div>
                            </div>
                            
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="order_management" value="order_management" class="form-check-input module-permission" id="order_management">
                                    <label class="form-check-label text-nowrap" for="order_management">{{translate('order_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="manage_order" value="manage_order" class="form-check-input module-permission order_management" id="manage_order">
                                            <label class="form-check-label text-nowrap" for="manage_order">{{translate('manage_order')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="product_management" value="product_management" class="form-check-input module-permission" id="product_management">
                                    <label class="form-check-label text-nowrap" for="product_management">{{translate('product_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="brand_setup" value="brand_setup" class="form-check-input module-permission product_management" id="brand_setup">
                                            <label class="form-check-label text-nowrap" for="brand_setup">{{translate('brand_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="category_setup" value="category_setup" class="form-check-input module-permission product_management" id="category_setup">
                                            <label class="form-check-label text-nowrap" for="category_setup">{{translate('category_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="product_setup" value="product_setup" class="form-check-input module-permission product_management" id="product_setup">
                                            <label class="form-check-label text-nowrap" for="product_setup">{{translate('product_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="product_approval" value="product_approval" class="form-check-input module-permission product_management" id="product_approval">
                                            <label class="form-check-label text-nowrap" for="product_approval">{{translate('product_approval')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="promotion_management" value="promotion_management" class="form-check-input module-permission" id="promotion_management">
                                    <label class="form-check-label text-nowrap" for="promotion_management">{{translate('promotion_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="banner" value="banner" class="form-check-input module-permission promotion_management" id="banner">
                                            <label class="form-check-label text-nowrap" for="banner">{{translate('banner')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="display" value="display" class="form-check-input module-permission promotion_management" id="display">
                                            <label class="form-check-label text-nowrap" for="display">{{translate('display')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="coupons" value="coupons" class="form-check-input module-permission promotion_management" id="coupons">
                                            <label class="form-check-label text-nowrap" for="coupons">{{translate('coupons')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="send_notification" value="send_notification" class="form-check-input module-permission promotion_management" id="send_notification">
                                            <label class="form-check-label text-nowrap" for="send_notification">{{translate('send_notification')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="report_management" value="report_management" class="form-check-input module-permission" id="report_management">
                                    <label class="form-check-label text-nowrap" for="report_management">{{translate('report_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="sales_report" value="sales_report" class="form-check-input module-permission report_management" id="sales_report">
                                            <label class="form-check-label text-nowrap" for="sales_report">{{translate('sales_report')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="order_report" value="order_report" class="form-check-input module-permission report_management" id="order_report">
                                            <label class="form-check-label text-nowrap" for="order_report">{{translate('order_report')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="earning_report" value="earning_report" class="form-check-input module-permission report_management" id="earning_report">
                                            <label class="form-check-label text-nowrap" for="earning_report">{{translate('earning_report')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="user_management" value="user_management" class="form-check-input module-permission" id="user_management">
                                    <label class="form-check-label text-nowrap" for="user_management">{{translate('user_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="customer_list" value="customer_list" class="form-check-input module-permission user_management" id="customer_list">
                                            <label class="form-check-label text-nowrap" for="customer_list">{{translate('customer_list')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="vender_list" value="vender_list" class="form-check-input module-permission user_management" id="vender_list">
                                            <label class="form-check-label text-nowrap" for="vender_list">{{translate('vender_list')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="serviceman_list" value="serviceman_list" class="form-check-input module-permission user_management" id="serviceman_list">
                                            <label class="form-check-label text-nowrap" for="serviceman_list">{{translate('serviceman_list')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="coustomer_wallet" value="coustomer_wallet" class="form-check-input module-permission user_management" id="coustomer_wallet">
                                            <label class="form-check-label text-nowrap" for="coustomer_wallet">{{translate('coustomer_wallet')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="product_reviews" value="product_reviews" class="form-check-input module-permission user_management" id="product_reviews">
                                            <label class="form-check-label text-nowrap" for="product_reviews">{{translate('product_reviews')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="employees" value="employees" class="form-check-input module-permission user_management" id="employees">
                                            <label class="form-check-label text-nowrap" for="employees">{{translate('employees')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="system_management" value="system_management" class="form-check-input module-permission" id="system_management">
                                    <label class="form-check-label text-nowrap" for="system_management">{{translate('system_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="business_setup" value="business_setup" class="form-check-input module-permission system_management" id="business_setup">
                                            <label class="form-check-label text-nowrap" for="business_setup">{{translate('business_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="branch_setup" value="branch_setup" class="form-check-input module-permission system_management" id="branch_setup">
                                            <label class="form-check-label text-nowrap" for="branch_setup">{{translate('branch_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="pages_media" value="pages_media" class="form-check-input module-permission system_management" id="pages_media">
                                            <label class="form-check-label text-nowrap" for="pages_media">{{translate('pages_media')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                </div>

                <div class="btn--container justify-content-end mt-4">
                    <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('Submit')}}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="card--header">
                <h5 class="card-title">{{translate('employee_roles_table')}} <span class="badge badge-soft-primary">{{count($adminRoles)}}</span></h5>
                <form action="{{url()->current()}}" method="GET">
                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search"
                            class="form-control"
                            placeholder="{{translate('Search by Role Name')}}" aria-label="Search" required autocomplete="off">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text">
                                {{translate('Search')}}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless mb-0" id="dataTable" cellspacing="0">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('SL')}}</th>
                        <th>{{translate('role_name')}}</th>
                        <th>{{translate('modules')}}</th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($adminRoles as $k=>$role)
                        <tr>
                            <td>{{$k+1}}</td>
                            <td>{{$role['name']}}</td>
                            <td class="text-capitalize">
                                <div class="max-w-300px">
                                    @if($role['module_access']!=null)
                                        @php($comma = '')
                                        @foreach((array)json_decode($role['module_access']) as $module)
                                            {{$comma}}{{ translate(str_replace('_',' ',$module)) }}
                                            @php($comma = ', ')
                                        @endforeach
                                    @endif
                                </div>
                            </td>
                            <td>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox"
                                           data-route="{{ route('admin.custom-role.status', [$role->id, $role->status == 1 ? 0 : 1]) }}"
                                           data-message="{{ $role->status? translate('you_want_to_disable_this_role'): translate('you_want_to_active_this_role') }}"
                                           class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $role->id }}"
                                        {{ $role->status == 0 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a href="{{route('admin.custom-role.update',[$role['id']])}}"
                                        class="action-btn"
                                        title="{{translate('Edit') }}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                       data-id="role-{{$role['id']}}"
                                       data-message="{{translate('Want to delete this role')}}?">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.custom-role.delete',[$role['id']])}}"
                                          method="post" id="role-{{$role['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @if(count($adminRoles) === 0)
                    <div class="text-center p-4">
                        <img class="mb-3 width-7rem" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                        <p class="mb-0">{{ translate('No data to show') }}</p>
                    </div>
                @endif
            </div>
        </div>
        <div>
            {{$adminRoles->links('pagination::bootstrap-4')}}
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/custom-role.js') }}"></script>
@endpush
