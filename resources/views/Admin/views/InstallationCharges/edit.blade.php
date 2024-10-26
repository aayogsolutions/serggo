@extends('Admin.layouts.app')

@section('title', translate('Update Installations'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/attribute.png')}}" class="w--24" alt="{{ translate('attribute') }}">
            </span>
            <span>
                {{translate('Installations')}} {{translate('update')}}
            </span>
        </h1>
    </div>
    <div class="card">
        <div class="card-body pt-2">
            <form action="{{route('admin.installation.update',[$installations['id']])}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                                    <input type="text" name="installation_name" class="form-control" value="{{ $installations['installation_name'] }}"  maxlength="255">
                                </div>
                                <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('Description')}}</label>
                                    <textarea name="description" class="form-control" value="">{{ $installations['installation_description'] }}</textarea>
                                </div>
                                <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('Charges')}}</label>
                                    <input type="number" name="charges" class="form-control" value="{{ $installations['installation_charges'] }}">
                                </div>
                    </div>
                </div>
                <div class="btn--container justify-content-end">
                    <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script_2')

@endpush