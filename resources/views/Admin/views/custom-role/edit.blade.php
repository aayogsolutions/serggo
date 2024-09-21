@extends('Admin.layouts.app')

@section('title', translate('update employee role'))

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

    <div class="card">
        <div class="card-body">
            <form id="submit-create-role" action="{{route('admin.custom-role.update',[$role['id']])}}" method="post">
                @csrf
                <div class="max-w-500px">
                    <div class="form-group">
                        <label class="form-label">{{translate('role_name')}}</label>
                        <input type="text" name="name" value="{{$role['name']}}" class="form-control" id="name"
                                aria-describedby="emailHelp"
                                placeholder="{{translate('Ex')}} : {{translate('Store')}}">
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
                                    <input type="checkbox" name="dashboard_management" value="dashboard_management" class="form-check-input module-permission" id="dashboard_management"
                                    {{in_array('dashboard_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="dashboard_management">{{translate('dashboard_management')}}</label>
                                </div>
                            </div>
                            
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="order_management" value="order_management" class="form-check-input module-permission" id="order_management"
                                    {{in_array('order_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="order_management">{{translate('order_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="manage_order" value="manage_order" class="form-check-input module-permission order_management" id="manage_order"
                                            {{in_array('manage_order',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="manage_order">{{translate('manage_order')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="product_management" value="product_management" class="form-check-input module-permission" id="product_management"
                                    {{in_array('product_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="product_management">{{translate('product_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="category_setup" value="category_setup" class="form-check-input module-permission product_management" id="category_setup"
                                            {{in_array('category_setup',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="category_setup">{{translate('category_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="product_setup" value="product_setup" class="form-check-input module-permission product_management" id="product_setup"
                                            {{in_array('product_setup',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="product_setup">{{translate('product_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="product_approval" value="product_approval" class="form-check-input module-permission product_management" id="product_approval"
                                            {{in_array('product_approval',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="product_approval">{{translate('product_approval')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="promotion_management" value="promotion_management" class="form-check-input module-permission" id="promotion_management"
                                    {{in_array('promotion_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="promotion_management">{{translate('promotion_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="banner" value="banner" class="form-check-input module-permission promotion_management" id="banner"
                                            {{in_array('banner',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="banner">{{translate('banner')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="coupons" value="coupons" class="form-check-input module-permission promotion_management" id="coupons"
                                            {{in_array('coupons',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="coupons">{{translate('coupons')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="send_notification" value="send_notification" class="form-check-input module-permission promotion_management" id="send_notification"
                                            {{in_array('send_notification',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="send_notification">{{translate('send_notification')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="offers" value="offers" class="form-check-input module-permission promotion_management" id="offers"
                                            {{in_array('offers',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="offers">{{translate('offers')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="category_discount" value="category_discount" class="form-check-input module-permission promotion_management" id="category_discount"
                                            {{in_array('category_discount',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="category_discount">{{translate('category_discount')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="report_management" value="report_management" class="form-check-input module-permission" id="report_management"
                                    {{in_array('report_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="report_management">{{translate('report_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="sales_report" value="sales_report" class="form-check-input module-permission report_management" id="sales_report"
                                            {{in_array('sales_report',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="sales_report">{{translate('sales_report')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="order_report" value="order_report" class="form-check-input module-permission report_management" id="order_report"
                                            {{in_array('order_report',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="order_report">{{translate('order_report')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="earning_report" value="earning_report" class="form-check-input module-permission report_management" id="earning_report"
                                            {{in_array('earning_report',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="earning_report">{{translate('earning_report')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="user_management" value="user_management" class="form-check-input module-permission" id="user_management"
                                    {{in_array('user_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="user_management">{{translate('user_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="customer_list" value="customer_list" class="form-check-input module-permission user_management" id="customer_list"
                                            {{in_array('customer_list',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="customer_list">{{translate('customer_list')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="vender_list" value="vender_list" class="form-check-input module-permission user_management" id="vender_list"
                                            {{in_array('vender_list',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="vender_list">{{translate('vender_list')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="serviceman_list" value="serviceman_list" class="form-check-input module-permission user_management" id="serviceman_list"
                                            {{in_array('serviceman_list',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="serviceman_list">{{translate('serviceman_list')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="coustomer_wallet" value="coustomer_wallet" class="form-check-input module-permission user_management" id="coustomer_wallet"
                                            {{in_array('coustomer_wallet',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="coustomer_wallet">{{translate('coustomer_wallet')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="product_reviews" value="product_reviews" class="form-check-input module-permission user_management" id="product_reviews"
                                            {{in_array('product_reviews',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="product_reviews">{{translate('product_reviews')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="employees" value="employees" class="form-check-input module-permission user_management" id="employees"
                                            {{in_array('employees',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="employees">{{translate('employees')}}</label>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                            <div class="main-check-item">
                                <div class="form-group form-check form--check">
                                    <input type="checkbox" name="system_management" value="system_management" class="form-check-input module-permission" id="system_management"
                                    {{in_array('system_management',(array)json_decode($role['module_access']))?'checked':''}}>
                                    <label class="form-check-label text-nowrap" for="system_management">{{translate('system_management')}}</label>
                                </div>
                            </div>
                            <ul>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="business_setup" value="business_setup" class="form-check-input module-permission system_management" id="business_setup"
                                            {{in_array('business_setup',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="business_setup">{{translate('business_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="branch_setup" value="branch_setup" class="form-check-input module-permission system_management" id="branch_setup"
                                            {{in_array('branch_setup',(array)json_decode($role['module_access']))?'checked':''}}>
                                            <label class="form-check-label text-nowrap" for="branch_setup">{{translate('branch_setup')}}</label>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="sub-check-item">
                                        <div class="form-group form-check form--check">
                                            <input type="checkbox" name="pages_media" value="pages_media" class="form-check-input module-permission system_management" id="pages_media"
                                            {{in_array('pages_media',(array)json_decode($role['module_access']))?'checked':''}}>
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
                    <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src="{{ asset('assets/admin/js/custom-role.js') }}"></script>
    <script>
        "use strict";

        $('#submit-create-role').on('submit',function(e){
            var fields = $("input.module-permission").serializeArray();
            if (fields.length === 0)
            {
                <?php
                    flash()->warning("Select minimum one selection box");
                ?>
                return false;
            }else{
                $('#submit-create-role').submit();
            }
        });
    </script>
@endpush
