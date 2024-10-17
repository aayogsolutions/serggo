@extends('Admin.layouts.app')

@section('title', translate('OTP Setup'))

@section('content')
<div class="content container-fluid">
    @include('Admin.views.business-settings.partial.business-settings-navmenu')

    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.otp-setup-update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 col-sm-4">
                                <div class="form-group">
                                    <label class="input-label" for="otp_resend_time">{{translate('OTP resend time')}}
                                        <span class="text-danger">( {{ translate('in second') }} )</span>
                                        <i class="tio-info-outined"
                                           data-toggle="tooltip"
                                           data-placement="top"
                                           title="{{ translate('If the user fails to get the OTP within a certain time, user can request a resend.') }}">
                                        </i>
                                    </label>
                                    <input type="number" min="1" value="{{$timer}}"
                                           name="otp_resend_time" class="form-control" placeholder="" required>
                                </div>
                            </div>
                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

