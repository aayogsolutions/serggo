@extends('Admin.layouts.app')

@section('title', translate('Service Update Tag'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/attribute.png')}}" class="w--24" alt="{{ translate('attribute') }}">
            </span>
            <span>
                {{translate('Service')}}  {{translate('tag')}} {{translate('update')}}
            </span>
        </h1>
    </div>
    <div class="card">
        <div class="card-body pt-2">
            <form action="{{route('admin.service.tag.update',[$tag['id']])}}" method="post">
                @csrf
                <div class="row">
                    <div class="col-12">
                        <div class="form-group lang_form" id="-form">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                            <input type="text" name="name" class="form-control" value="{{ $tag['name'] }}" placeholder="{{translate('New tag')}}" maxlength="255">
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