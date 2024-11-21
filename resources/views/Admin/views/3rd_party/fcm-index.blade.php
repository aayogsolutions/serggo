@extends('Admin.layouts.app')

@section('title', translate('FCM Settings'))

@section('content')
    <div class="content container-fluid">

        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center">
                <img width="20" class="avatar-img" src="{{asset('assets/admin/img/firebase.png')}}" alt="">
                <span class="page-header-title ml-2 mt-2">
                    {{translate('firebase_push_notification_setup')}}
                </span>
            </h2>
        </div>

        <div class="card">
            <div class=" card-header-shadow pb-0">
                <div class="d-flex flex-wrap justify-content-between w-100 row-gap-1">
                    <ul class="nav nav-tabs nav--tabs border-0 ml-3">
                        <li class="nav-item mr-2 mr-md-4">
                            <a href="{{ route('admin.business-settings.web-app.third-party.fcm-index') }}" class="nav-link pb-2 px-0 pb-sm-3 active" data-slide="1">
                                <img src="{{asset('assets/admin/img/notify.png')}}" alt="">
                                <span>{{translate('Push Notification')}}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.business-settings.web-app.third-party.fcm-config') }}" class="nav-link pb-2 px-0 pb-sm-3" data-slide="2">
                                <img src="{{asset('assets/admin/img/firebase2.png')}}" alt="">
                                <span>{{translate('Firebase Configuration')}}</span>
                            </a>
                        </li>
                    </ul>
                    <div class="py-1">
                        <div class="tab--content">
                            <div class="item show text-primary d-flex flex-wrap align-items-center" type="button" data-toggle="modal" data-target="#push-notify-modal">
                                <strong class="mr-2">{{translate('Read Documentation')}}</strong>
                                <div class="blinkings">
                                    <i class="tio-info-outined"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="push-notify">

                        <form action="{{route('admin.business-settings.web-app.third-party.update-fcm-messages')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="lang_form" id="default-form">

                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="place_status">
                                                <input type="checkbox" name="place_status" class="toggle-switch-input" id="place_status" {{$order_place_message['status'] == 0 ? 'checked' : '' }}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('order')}} {{translate('Place')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="place_message" class="form-control">{{$order_place_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="approval_status">
                                                <input type="checkbox" name="approval_status" class="toggle-switch-input" id="approval_status" {{$order_approval_message['status'] == 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block"> {{translate('order')}} {{translate('approval')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="approval_message" class="form-control">{{$order_approval_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="rejected_status">
                                                <input type="checkbox" name="rejected_status" class="toggle-switch-input" id="rejected_status" {{$order_rejected_message['status'] == 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block"> {{translate('order')}} {{translate('rejected')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="rejected_message" class="form-control">{{$order_rejected_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="processing_status">
                                                <input type="checkbox" name="processing_status" class="toggle-switch-input" id="processing_status" {{$order_processing_message['status'] == 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('order')}} {{translate('Packaging')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="processing_message" class="form-control">{{$order_processing_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="out_for_delivery">
                                                <input type="checkbox" name="out_for_delivery_status" class="toggle-switch-input" id="out_for_delivery" {{$out_for_delivery_message['status'] == 0 ?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('order')}} {{translate('out_for_delivery')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="out_for_delivery_message" class="form-control">{{$out_for_delivery_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="delivered_status">
                                                <input type="checkbox" name="delivered_status" class="toggle-switch-input" id="delivered_status" {{$order_delivered_message['status'] == 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('order')}} {{translate('delivered')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="delivered_message" class="form-control">{{$order_delivered_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="customer_notify">
                                                <input type="checkbox" name="customer_notify_status" class="toggle-switch-input" id="customer_notify" {{ $customer_notify_message['status'] == 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('DeliveryMan assign notification for customer')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="customer_notify_message" class="form-control">{{$customer_notify_message['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="delivery_boy_assign">
                                                <input type="checkbox" name="delivery_boy_assign_status" class="toggle-switch-input" id="delivery_boy_assign" {{$delivery_boy_assign_message['status'] == 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('deliveryman')}} {{translate('assign')}} {{translate('message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="delivery_boy_assign_message" class="form-control">{{$delivery_boy_assign_message['message']}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="returned_status">
                                                <input type="checkbox" name="returned_status" class="toggle-switch-input" id="returned_status" {{ $returned_message['status'] == 0 ?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('Order_returned_message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="returned_message" class="form-control">{{$returned_message['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="failed_status">
                                                <input type="checkbox" name="failed_status" class="toggle-switch-input" id="failed_status" {{ $failed_message['status']== 0 ?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('Order_failed_message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="failed_message" class="form-control">{{$failed_message['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="canceled_status">
                                                <input type="checkbox" name="canceled_status" class="toggle-switch-input" id="canceled_status" {{ $canceled_message['status']== 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('Order_canceled_message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="canceled_message" class="form-control">{{$canceled_message['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    <!-- <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="dm_order_processing_status">
                                                <input type="checkbox" name="dm_order_processing_status" class="toggle-switch-input" id="dm_order_processing_status" {{ $deliveryman_order_processing_message['status']== 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('deliveryman_order_processing_message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="dm_order_processing_message" class="form-control">{{$deliveryman_order_processing_message['message']??''}}</textarea>
                                        </div>
                                    </div> -->

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="add_fund_status">
                                                <input type="checkbox" name="add_fund_status" class="toggle-switch-input" id="add_fund_status" {{ $add_fund_wallet_message['status']== 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('add_fund_wallet_message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="add_fund_message" class="form-control">{{$add_fund_wallet_message['message']??''}}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label class="toggle-switch d-flex align-items-center mb-3" for="add_fund_bonus_status">
                                                <input type="checkbox" name="add_fund_bonus_status" class="toggle-switch-input" id="add_fund_bonus_status" {{ $add_fund_wallet_bonus_message['status']== 0 ? 'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                                <span class="toggle-switch-content">
                                                    <span class="d-block">{{translate('add_fund_wallet_bonus_message')}}</span>
                                                </span>
                                            </label>
                                            <textarea name="add_fund_bonus_message" class="form-control">{{$add_fund_wallet_bonus_message['message']??''}}</textarea>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="btn--container justify-content-end">
                                <button type="reset" class="btn btn--reset">{{translate('clear')}}</button>
                                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="push-notify-modal">
            <div class="modal-dialog status-warning-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true" class="tio-clear"></span>
                        </button>
                    </div>
                    <div class="modal-body pb-5 pt-0">
                        <div class="single-item-slider owl-carousel">
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('assets/admin/img/firebase/slide-1.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Go_to_Firebase_Console')}}</h5>
                                    </div>
                                    <ul>
                                        <li>
                                            {{translate('Open_your_web_browser_and_go_to_the_Firebase_Console')}}
                                            <a href="#" class="text--underline">
                                                {{translate('(https://console.firebase.google.com/)')}}
                                            </a>
                                        </li>
                                        <li>
                                            {{translate("Select_the_project_for_which_you_want_to_configure_FCM_from_the_Firebase_Console_dashboard.")}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('assets/admin/img/firebase/slide-2.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Navigate_to_Project_Settings')}}</h5>
                                    </div>
                                    <ul>
                                        <li>
                                            {{translate('In_the_left-hand_menu,_click_on_the_"Settings"_gear_icon,_and_then_select_"Project_settings"_from_the_dropdown.')}}
                                        </li>
                                        <li>
                                            {{translate('In_the_Project_settings_page,_click_on_the_"Cloud_Messaging"_tab_from_the_top_menu.')}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('assets/admin/img/firebase/slide-3.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Obtain_All_The_Information_Asked!')}}</h5>
                                    </div>
                                    <ul>
                                        <li>
                                            {{translate('In_the_Firebase_Project_settings_page,_click_on_the_"General"_tab_from_the_top_menu.')}}
                                        </li>
                                        <li>
                                            {{translate('Under_the_"Your_apps"_section,_click_on_the_"Web"_app_for_which_you_want_to_configure_FCM.')}}
                                        </li>
                                        <li>
                                            {{translate('Then_Obtain_API_Key')}}
                                        </li>
                                    </ul>
                                    <p>
                                        {{translate('Note:_Please_make_sure_to_use_the_obtained_information_securely_and_in_accordance_with_Firebase_and_FCM_documentation,_terms_of_service,_and_any_applicable_laws_and_regulations.')}}
                                    </p>

                                </div>
                            </div>

                            <div class="item">
                                <div class="mb-20">
                                    <div class="text-center">
                                        <img src="{{asset('assets/admin/img/email-templates/3.png')}}" alt="" class="mb-3">
                                        <h5 class="modal-title mb-2">{{translate('Write_a_message_in_the_Notification_Body')}}</h5>
                                    </div>
                                    <p>
                                        {{ translate('you_can_add_your_message_using_placeholders_to_include_dynamic_content._Here_are_some_examples_of_placeholders_you_can_use:') }}
                                    </p>
                                    <ul>
                                        <li>
                                            {userName}: {{ translate('the_name_of_the_user.') }}
                                        </li>
                                        <li>
                                            {orderId}: {{ translate('the_order_id.') }}
                                        </li>
                                        <li>
                                            {storeName}: {{ translate('store_name.') }}
                                        </li>
                                        <li>
                                            {deliveryManName}: {{ translate('deliveryman_name.') }}
                                        </li>
                                    </ul>
                                    <div class="btn-wrap">
                                        <button type="submit" class="btn btn--primary w-100" data-dismiss="modal" data-toggle="modal" data-target="#firebase-modal-2">{{translate('Got It')}}</button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="slide-counter"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')
    <script>
        $('[data-slide]').on('click', function(){
            let serial = $(this).data('slide')
            $(`.tab--content .item`).removeClass('show')
            $(`.tab--content .item:nth-child(${serial})`).addClass('show')
        })
    </script>

    

@endpush
