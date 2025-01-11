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
            <form action="{{route('admin.banners.home.update',[$banner['id']])}}" method="post" enctype="multipart/form-data">
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
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">
                                        {{translate('UI')}} {{translate('type')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="type" class="form-control show-item">
                                        <option value="user_product" {{$banner->ui_type == 'user_product' ? 'selected' : ''}}>{{translate('user_product')}}</option>
                                        <option value="user_service" {{$banner->ui_type == 'user_service' ? 'selected' : ''}}>{{translate('user_service')}}</option>
                                        <option value="amc" {{$banner->ui_type == 'amc' ? 'selected' : ''}}>{{translate('AMC')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('background_color')}}</label>
                                    <input type="color" name="background_color" value="{{ $banner->background_color }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('font_color')}}</label>
                                    <input type="color" name="font_color" value="{{ $banner->font_color}}" class="form-control" required>
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
                            <label class="m-auto">
                                <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*,video/*" hidden>
                                @if($banner->attechment_type == 'image')
                                    <img id="viewer" class="initial-24" src="{{asset($banner->attechment)}}" alt="{{ translate('banner image') }}" onerror="this.src='{{asset('assets/admin/img/upload-horizontal.jpg')}}'"/>
                                    <video class="initial-24" id="viewervideo" src="" style="display: none;" autoplay muted loop></video>
                                    <input type="hidden" name="width" id="videoWidth">
                                    <input type="hidden" name="height" id="videoHeight">
                                @else
                                    <img class="initial-24" id="viewer" src="{{asset('assets/admin/img/upload-horizontal.jpg')}}" alt="{{ translate('banner image') }}" style="display: none;"/>
                                    <video class="initial-24" id="viewervideo" src="{{asset($banner->attechment)}}" autoplay muted loop></video>
                                    <input type="hidden" name="width" id="videoWidth">
                                    <input type="hidden" name="height" id="videoHeight">
                                @endif
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