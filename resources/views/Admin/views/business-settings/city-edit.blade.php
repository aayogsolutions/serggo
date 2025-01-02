@extends('Admin.layouts.app')

@section('title', translate('Update_cities'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('public/assets/admin/img/edit.png')}}" class="w--24" alt="">
                </span>
                <span>
                    {{translate('City')}} {{translate('update')}}
                </span>
            </h1>
        </div>
        @include('Admin.views.business-settings.partial.business-settings-navmenu')
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.business-settings.store.city.update',[$city['id']])}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">{{translate('name')}} <span class="text-danger">*</span></label>
                                <input type="text" value="{{$city['name']}}" name="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label">{{translate('range')}} <span class="text-danger">*</span></label>
                                <input type="number" value="{{$city['km']}}" name="km" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                    </div>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')

@endpush
