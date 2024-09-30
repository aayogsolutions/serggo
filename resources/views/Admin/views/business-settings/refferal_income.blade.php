@extends('Admin.layouts.app')

@section('title', translate('Reffral Income Distributing setting'))

@section('content')
    <div class="content container-fluid">
        @include('Admin.views.business-settings.partial.business-settings-navmenu')
        <div class="tab-content">
            <div class="tab-pane fade show active" id="business-setting">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title d-flex align-items-center">
                            <span class="card-header-icon mb-1 mr-2">
                                <img src="{{asset('public/assets/admin/img/bag.png')}}" class="w--17" alt="">
                            </span>
                            <span>{{translate('Reffral Income Distributing setting')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.store.referral-income-setup-update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                @php($value = Helpers_get_business_settings('Helpers_get_business_settings'))
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{translate('How many Days')}}</label>
                                        <input type="number" value="{{$value}}" name="days" class="form-control" placeholder="" required>
                                    </div>
                                </div>
                            </div>
                            <div class="btn--container justify-content-end mt-5">
                                <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                <button type="{{env('APP_MODE')!='demo'?'submit':'button'}}"
                                    class="btn btn--primary call-demo">{{translate('save')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('script_2')
    

   
@endpush
