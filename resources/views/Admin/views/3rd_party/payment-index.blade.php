@extends('Admin.layouts.app')

@section('title', translate('Payment Setup'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            @include('Admin.views.3rd_party.partial.third-party-api-navmenu')
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3">{{translate('payment')}} {{translate('method')}}</h5>
                        
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['cash_on_delivery'])}}" method="post">
                            @csrf
                            @if(isset($cod))

                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('cash_on_delivery')}}</strong>
                                    </label>
                                </div>

                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" name="status"  value="0" {{ $cod == 0 ? 'checked' : ''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('active')}}</span>
                                    </label>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="1" {{ $cod == 1 ? 'checked' : ''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('inactive')}}</span>
                                    </label>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('cash_on_delivery')}}</strong>
                                    </label>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-uppercase mb-3">{{translate('payment')}} {{translate('method')}}</h5>
                        
                        <form action="{{route('admin.business-settings.web-app.payment-method-update',['digital_payment'])}}"method="post">
                            @csrf
                            @if(isset($digital_payment))

                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('digital')}} {{translate('payment')}}</strong>
                                    </label>
                                </div>

                                <div class="d-flex flex-wrap mb-4">
                                    <label class="form-check mr-2 mr-md-4">
                                        <input class="form-check-input" type="radio" name="status"  value="0" {{ $digital_payment ==0?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('active')}}</span>
                                    </label>
                                    <label class="form-check">
                                        <input class="form-check-input" type="radio" name="status" value="1" {{ $digital_payment==1?'checked':''}}>
                                        <span class="form-check-label text--title pl-2">{{translate('inactive')}}</span>
                                    </label>
                                </div>

                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('save')}}</button>
                                </div>
                            @else
                                <div class="form-group">
                                    <label class="form-label text--title">
                                        <strong>{{translate('digital')}} {{translate('payment')}}</strong>
                                    </label>
                                </div>
                                <div class="text-right">
                                    <button type="submit" class="btn btn-primary px-5">{{translate('configure')}}</button>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="row digital_payment_methods mt-3 g-3" id="payment-gatway-cards">
            @foreach($data_values as $payment)
                <div class="col-md-6 mb-5">
                    <div class="card">
                        <form action="{{route('admin.business-settings.web-app.payment-config-update')}}" method="POST" id="{{$payment->key_name}}-form" enctype="multipart/form-data">
                            @csrf
                            <div class="card-header d-flex flex-wrap align-content-around">
                                <h5>
                                    <span class="text-uppercase">{{str_replace('_',' ',$payment->key_name)}}</span>
                                </h5>
                                <label class="switch--custom-label toggle-switch toggle-switch-sm d-inline-flex">
                                    <span class="mr-2 switch--custom-label-text text-primary on text-uppercase">on</span>
                                    <span class="mr-2 switch--custom-label-text off text-uppercase">off</span>
                                    <input type="checkbox" name="status" value="1"
                                           class="toggle-switch-input" {{$payment['is_active']==1?'checked':''}}>
                                    <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                </label>
                            </div>

                            @php($additional_data = $payment['additional_data'] != null ? json_decode($payment['additional_data']) : [])
                            <div class="card-body">
                                <div class="payment--gateway-img">
                                    <img class="h--92px"
                                         src="{{ Helpers_onErrorImage($additional_data != null ? $additional_data->gateway_image : '',
                                               asset('storage/app/public/payment_modules/gateway_image') . '/' . ($additional_data != null ? $additional_data->gateway_image : ''),
                                               asset('assets/admin/img/placeholder.png'), 'payment_modules/gateway_image/')}}"
                                         alt="{{ translate('gateway_image') }}">
                                </div>

                                <input name="gateway" value="{{$payment->key_name}}" class="d-none">

                                @php($mode = json_decode($payment->live_values)->mode)
                                <div class="form-floating mb-2">
                                    <select class="js-select form-control theme-input-style w-100" name="mode">
                                        <option value="live" {{$mode=='live'?'selected':''}}>Live</option>
                                        <option value="test" {{$mode=='test'?'selected':''}}>Test</option>
                                    </select>
                                </div>

                                @php($skip = ['gateway','mode','status'])
                                @foreach(json_decode($payment->live_values) as $key => $value)
                                    @if(!in_array($key,$skip))
                                        <div class="form-floating mb-2">
                                            <label for="exampleFormControlInput1"
                                                   class="form-label">{{ucwords(str_replace('_',' ',$key))}}*</label>
                                            <input type="text" class="form-control"
                                                   name="{{$key}}"
                                                   placeholder="{{ucwords(str_replace('_',' ',$key))}} *"
                                                   value="{{env('APP_ENV')=='demo'?'':$value}}">
                                        </div>
                                    @endif
                                @endforeach

                                <div class="form-floating mb-2">
                                    <label for="exampleFormControlInput1"
                                           class="form-label">{{translate('payment_gateway_title')}}</label>
                                    <input type="text" class="form-control" name="gateway_title" placeholder="{{translate('payment_gateway_title')}}"
                                           value="{{$additional_data != null ? $additional_data->gateway_title : ''}}">
                                </div>

                                <div class="form-floating mb-2">
                                    <label for="exampleFormControlInput1"
                                           class="form-label">{{translate('choose logo')}}</label>
                                    <input type="file" class="form-control" name="gateway_image" accept=".jpg, .png, .jpeg|image/*">
                                </div>

                                <div class="text-right mb-2">
                                    <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                            class="btn btn-primary px-5 call-demo">{{translate('save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('script_2')

<script>
    "use strict";

    $(document).on('change', 'input[name="gateway_image"]', function () {
        console.log('aa');
        var $input = $(this);
        var $form = $input.closest('form');
        var gatewayName = $form.attr('id');

        if (this.files && this.files[0]) {
            var reader = new FileReader();
            var $imagePreview = $form.find('.payment--gateway-img img');

            reader.onload = function (e) {
                $imagePreview.attr('src', e.target.result);
            }

            reader.readAsDataURL(this.files[0]);
        }
    });

    function checkedFunc() {
        $('.switch--custom-label .toggle-switch-input').each( function() {
            if(this.checked) {
                $(this).closest('.switch--custom-label').addClass('checked')
            }else {
                $(this).closest('.switch--custom-label').removeClass('checked')
            }
        })
    }
    checkedFunc()
    $('.switch--custom-label .toggle-switch-input').on('change', checkedFunc)
    
</script>
@endpush



