@extends('Admin.layouts.app')

@section('title', translate('Reffral Income setting'))

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
                            <span>{{translate('Reffral Income setting')}}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{route('admin.business-settings.store.referral-income-setup-update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('refferal_bonus')}}
                                            <span class="form-label-secondary text-danger d-inline ml-1" data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{translate('Set the amount of Reffral Bounce. When Someone Join with Reffral Code. Who is Referred New Joinee get this bounce ')}}">
                                                <img src="{{asset('assets/admin/img/info-circle.svg')}}" alt="info">
                                            </span>
                                        </label>
                                        <input type="number" value="{{$value->bonus}}" name="bonus" class="form-control" placeholder="" required>
                                    </div>
                                </div>
                                <div class="col-md-8 col-sm-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('Refferal Content')}}
                                            <span class="form-label-secondary text-danger d-inline ml-1" data-toggle="tooltip" data-placement="right"
                                                    data-original-title="{{translate('Set the Content for refferal which is displayed when user share our app link to other in scail media')}}">
                                                <img src="{{asset('assets/admin/img/info-circle.svg')}}" alt="info">
                                            </span>
                                        </label>
                                        <textarea name="content" class="form-control" placeholder="Enter Content" required>{{$value->content}}</textarea>
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
