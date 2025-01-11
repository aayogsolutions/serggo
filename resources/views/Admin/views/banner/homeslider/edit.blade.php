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
            <form action="{{route('admin.banners.homeslider.update',[$banner['id']])}}" method="post" enctype="multipart/form-data">
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
                                    <select name="type" class="form-control" disabled>
                                        <option value="user_product" {{$banner->ui_type == 'user_product' ? 'selected' : ''}}>{{translate('user_product')}}</option>
                                        <option value="user_service" {{$banner->ui_type == 'user_service' ? 'selected' : ''}}>{{translate('user_service')}}</option>
                                        <option value="amc" {{$banner->ui_type == 'amc' ? 'selected' : ''}}>{{translate('AMC')}}</option>
                                    </select>
                                    <input type="text" value="{{$banner->ui_type}}" name="type" hidden>
                                </div>
                            </div>
                            @if($banner->ui_type == 'user_product')
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('item')}} {{translate('type')}}<span
                                                class="input-label-secondary">*</span></label>
                                        <select name="item_type" class="form-control show-item">
                                            <option value="product" {{$banner->item_type == 'product' ? 'selected' : ''}}>{{translate('product')}}</option>
                                            <option value="category" {{$banner->item_type == 'category' ? 'selected' : ''}}>{{translate('category')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0" id="type-product">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('product')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="product_id" class="form-control js-select2-custom">
                                            @foreach($products as $product)
                                                <option value="{{$product['id']}}" {{$banner->item_id == $product['id'] ? 'selected' : ''}}>{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group mb-0" id="type-category" style="display:none;">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('category')}} <span
                                                class="input-label-secondary">*</span></label>
                                        <select name="category_id" class="form-control js-select2-custom">
                                            @foreach($categories as $category)
                                                <option value="{{$category['id']}}" {{$banner->item_id == $category['id'] ? 'selected' : ''}}>{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @elseif($banner->ui_type == 'user_service')
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">
                                            {{translate('Category')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="category_id" class="form-control js-select2-custom" id="service_catogory_id">
                                            <option selected disabled>Select Category</option>
                                            @foreach($products as $servicecategory)
                                                <option value="{{$servicecategory['id']}}" {{ json_decode($banner->item_detail)->parent_id == $servicecategory['id'] ? 'selected' : ''}}>{{$servicecategory['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">
                                            {{translate('Sub Category')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="sub_category_id" class="form-control js-select2-custom" id="service_sub_catogory_id">
                                            <option selected disabled>Select Sub Category</option>
                                            @foreach($categories as $servicecategory)
                                                <option value="{{$servicecategory['id']}}" {{ $banner->item_id == $servicecategory['id'] ? 'selected' : ''}}>{{$servicecategory['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
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

<script>
    $('#service_catogory_id').change(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "get",
            url: "{{route('admin.service.get-categories')}}",
            contentType: false,
            data: {
                parent_id: $('#service_catogory_id').val()
            },
            success: function(data) {
                console.log(data.options);
                console.log(data.option);
                console.log($('#catogory_id').val());
                $('#service_sub_catogory_id').html(data.options);
            }
        });
    });
</script>
@endpush