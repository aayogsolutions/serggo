@extends('Admin.layouts.app')

@section('title', translate('Update banner'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/edit.png')}}" class="w--20" alt="{{ translate('banner') }}">
            </span>
            <span>
                {{translate('Update Banner')}}
            </span>
        </h1>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.vendor.banner.update',[$banner['id']])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                    <input type="text" name="title" value="{{ $banner->title }}" class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column justify-content-center h-100">
                            <h5 class="text-center mb-3 text--title text-capitalize">
                                {{translate('banner')}} {{translate('image')}}
                                <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                            </h5>
                            <label class="upload--vertical">
                                <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                <img class="" id="viewer" src="{{asset($banner->attechment)}}" alt="{{ translate('banner image') }}" onerror="this.src='{{asset('assets/admin/img/upload-horizontal.jpg')}}'"/>
                                <video class="" id="viewervideo" src="" style="display: none;" autoplay loop></video>
                                <input type="hidden" name="width" id="videoWidth">
                                <input type="hidden" name="height" id="videoHeight">
                            </label>
                            
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset" onclick="location.reload()">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                        </div>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>
</div>

@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/banner.js') }}"></script>
@endpush