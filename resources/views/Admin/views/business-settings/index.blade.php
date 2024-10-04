@extends('Admin.layouts.app')

@section('title', translate('business settings'))

@section('content')
    <div class="content container-fluid">
        @include('Admin.views.business-settings.partial.business-settings-navmenu')
        
        <div class="tab-content">
            <div class="tab-pane fade show active" id="business-setting">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="maintainance-mode-toggle-bar d-flex flex-wrap justify-content-between border border-primary-70 rounded align-items-center p-2">
                            @php($config = Helpers_get_business_settings('maintenance_mode'))
                            <h5 class="text-capitalize m-0 text--info">
                                <i class="tio-settings-outlined"></i>
                                {{ translate('maintenance_mode') }}
                            </h5>
                            <label class="toggle-switch toggle-switch-sm">
                                <input type="checkbox" class="status toggle-switch-input" onclick="maintenance_mode()"
                                {{ isset($config) && $config == 0 ? 'checked' : '' }}>
                                <span class="toggle-switch-label text mb-0">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </div>
                        <div class="mt-2">
                            {{translate('*By turning on maintaince mode, all your app and customer side website will be off. Only admin panel and seller panel will be functional')}}
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title d-flex align-items-center">
                            <span class="card-header-icon mb-1 mr-2">
                                <img src="{{asset('assets/admin/img/bag.png')}}" class="w--17" alt="">
                            </span>
                            <span>{{translate('Business Information')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.store.update-setup')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @php($name = Helpers_get_business_settings('app_name'))
                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('app')}} {{translate('name')}}</label>
                                        <input type="text" name="app_name" value="{{$name}}" class="form-control"
                                               placeholder="{{ translate('App Name') }}" required>
                                    </div>
                                </div>
                                @php($phone = Helpers_get_business_settings('phone'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('phone')}}</label>
                                        <input type="text" value="{{$phone}}" name="phone" class="form-control" placeholder="" required>
                                    </div>
                                </div>
                                @php($email = Helpers_get_business_settings('email_address'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('email')}}</label>
                                        <input type="email" value="{{$email}}" name="email" class="form-control" placeholder="" required>
                                    </div>
                                </div>
                                @php($address = Helpers_get_business_settings('address'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                               for="exampleFormControlInput1">{{translate('address')}}</label>
                                        <input type="text" value="{{$address}}"
                                               name="address" class="form-control" placeholder=""
                                               required>
                                    </div>
                                </div>
                                @php($footer_text = Helpers_get_business_settings('footer_text'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label"
                                            for="exampleFormControlInput1">{{translate('footer')}} {{translate('text')}}</label>
                                        <input type="text" value="{{$footer_text}}"
                                            name="footer_text" class="form-control" placeholder=""
                                            required>
                                    </div>
                                </div>
                                @php($pagination_limit = Helpers_get_business_settings('pagination_limit'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('pagination')}} {{translate('limit')}}
                                        </label>
                                        <input type="number" value="{{$pagination_limit}}" name="pagination_limit" class="form-control" placeholder="0" required>
                                    </div>
                                </div>
                            </div>
                            @if(Auth('admins')->id() == 1)
                                <div class="row">
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <label>{{translate('logo')}}</label><small class="text-danger"> ( {{translate('ratio')}} 3:1 )</small>
                                            <div class="custom-file">
                                                <input type="file" name="logo" id="customFileEg1" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                    for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                                            </div>
                                            <div class="text-center">
                                                <img id="viewer" class="mt-4 border rounded mw-100 p-2"
                                                    src="{{ asset('Images/Business/').'/'.$logo }}"
                                                    onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'"
                                                    alt="{{ translate('logo') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <div class="form-group mb-0">
                                            <label>{{translate('Fav Icon')}}</label><small class="text-danger"> ( {{translate('ratio')}} 1:1 )</small>
                                            <div class="custom-file">
                                                <input type="file" name="fav_icon" id="customFileEg2" class="custom-file-input"
                                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                                <label class="custom-file-label"
                                                    for="customFileEg2">{{translate('choose')}} {{translate('file')}}</label>
                                            </div>
                                            <div class="text-center">
                                                <img id="viewer_2" class="mt-4 border rounded p-2 aspect-1 mw-145 object-cover"
                                                    src="{{ asset('Images/Business/').'/'.$favIcon }}"
                                                    onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'"
                                                    alt="{{ translate('fav_icon') }}"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="btn--container justify-content-end mt-5">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}" class="btn btn--primary call-demo">
                                    {{translate('save')}}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
       
@push('script_2')
    <script>
        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#' + viewer).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
        });

        $("#customFileEg2").change(function() {
            readURL(this, 'viewer_2');
        });
    </script>

    <script>
        function maintenance_mode() {
            Swal.fire({
                title: '{{ translate("Are you sure?") }}',
                text: '{{ translate("Be careful before you turn on/off maintenance mode") }}',
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#377dff',
                cancelButtonText: '{{translate("No")}}',
                confirmButtonText: '{{translate("Yes")}}',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $.get({
                        url: "{{route('admin.business-settings.store.maintenance-mode')}}",
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $('#loading').show();
                        },
                        success: function (data) {
                            if(data.message == 'Done')
                            {
                                location.reload();
                            }
                        },
                        complete: function () {
                            $('#loading').hide();
                        },
                    });
                } else {
                    location.reload();
                }
            })
        };

        function changeBusinessSettings(route) {
            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success(data.message);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function max_amount_status(route) {
            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                    if(data.status == 1){
                        toastr.success(data.message);
                    }else{
                        toastr.warning(data.message);
                    }
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }

        function partial_payment_status(route) {

            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    toastr.success(data.message);
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        }
    </script>
@endpush
