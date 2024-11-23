@extends('Admin.layouts.app')

@section('title', translate('home display content section'))

@push('css')
<style>
    .upload--vertical--preview {
        height: 50px;
    }

    .table-responsive {
        max-height: 700px !important;
    }
</style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/banner.png')}}" class="w--20" alt="{{ translate('banner') }}">
            </span>
            <span>
                {{translate('home display content section ')}}
            </span>
        </h1>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">
                                    {{translate('section title')}}
                                </label>
                                <input type="text" name="title" value="{{ translate($banner->title) }}" class="form-control" maxlength="255" disabled>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlSelect1">
                                    {{translate('UI')}} {{translate('type')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="type" class="form-control" disabled>
                                    <option>{{translate($banner->ui_type)}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlSelect1">{{translate('section')}} {{translate('type')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="section_type" class="form-control" disabled>
                                    @if($banner->ui_type == 'user_service')
                                        @if($banner->section_type == 'slider')
                                            <option>{{translate('Small banner')}}</option>
                                        @else
                                            <option>{{translate($banner->section_type)}}</option>
                                        @endif
                                    @else
                                        <option>{{translate($banner->section_type)}}</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="btn--container justify-content-end">
                        <button type="button" class="btn btn--reset" onclick="location.reload()">{{translate('reset')}}</button>
                        <button type="button" class="btn btn--primary" data-toggle="modal" data-target="#addmodel">{{translate('Add Content')}}</button>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addmodel" tabindex="-1" role="dialog" aria-labelledby="addmodel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('admin.display.add.content', $banner->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="{{ $banner->section_type }}">
                            <input type="hidden" name="ui_type" value="{{ $banner->ui_type }}">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{translate($banner->title)}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                @if($banner->ui_type == 'user_product' && $banner->section_type == 'cart')
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('Products')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="product_id" class="form-control">
                                            <option selected disabled>{{translate('select product')}}</option>
                                            @foreach($products as $key => $product)
                                            <option value="{{ $product->id }}">{{translate($product->name)}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @elseif($banner->ui_type == 'user_product' && $banner->section_type == 'box_section')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <h5 class="text-center mb-3 text--title text-capitalize">
                                                    {{translate('banner')}} {{translate('image')}}
                                                    <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                                                </h5>
                                                <label class="upload--vertical">
                                                    <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                                    <img class="" id="viewer" src="{{asset('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}" />
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="form-group mb-0">
                                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('item')}} {{translate('type')}}<span
                                                                class="input-label-secondary">*</span></label>
                                                        <select name="item_type" class="form-control show-item">
                                                            <option value="product">{{translate('product')}}</option>
                                                            <option value="category">{{translate('category')}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group mb-0" id="type-product">
                                                        <label class="input-label" for="exampleFormControlSelect1">
                                                            {{translate('product')}}
                                                            <span class="input-label-secondary">*</span>
                                                        </label>
                                                        <select name="product_id" class="form-control js-select2-custom">
                                                            <option selected disabled>Select Product</option>
                                                            @foreach($products as $product)
                                                            <option value="{{$product['id']}}">{{$product['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group mb-0" id="type-category" style="display:none;">
                                                        <label class="input-label" for="exampleFormControlSelect1">
                                                            {{translate('category')}}
                                                            <span class="input-label-secondary">*</span>
                                                        </label>
                                                        <select name="category_id" class="form-control js-select2-custom">
                                                            <option selected disabled>Select Category</option>
                                                            @foreach($categories as $category)
                                                            <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($banner->ui_type == 'user_product' && $banner->section_type == 'slider')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <h5 class="text-center mb-3 text--title text-capitalize">
                                                    {{translate('banner')}} {{translate('image')}}
                                                    <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                                                </h5>
                                                <label class="upload--vertical">
                                                    <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                                    <img class="" id="viewer" src="{{asset('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}" />
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="input-label" for="exampleFormControlSelect1">
                                                        {{translate('product')}}
                                                        <span class="input-label-secondary">*</span>
                                                    </label>
                                                    <select name="product_id" class="form-control js-select2-custom">
                                                        <option selected disabled>Select Product</option>
                                                        @foreach($products as $product)
                                                        <option value="{{$product['id']}}">{{$product['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($banner->ui_type == 'user_service' && $banner->section_type == 'slider')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <h5 class="text-center mb-3 text--title text-capitalize">
                                                    {{translate('banner')}} {{translate('image')}}
                                                    <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                                                </h5>
                                                <label class="upload--vertical">
                                                    <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                                    <img class="" id="viewer" src="{{asset('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}" />
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="input-label" for="exampleFormControlSelect1">
                                                        {{translate('Category')}}
                                                        <span class="input-label-secondary">*</span>
                                                    </label>
                                                    <select name="product_id" class="form-control js-select2-custom" id="catogory_id" required>
                                                        <option selected disabled>Select Category</option>
                                                        @foreach($products as $product)
                                                        <option value="{{$product['id']}}">{{$product['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($banner->ui_type == 'user_service' && $banner->section_type == 'box_section')
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <h5 class="text-center mb-3 text--title text-capitalize">
                                                    {{translate('banner')}} {{translate('image')}}
                                                    <small class="text-danger">* ( {{translate('ratio')}} 1:1 )</small>
                                                </h5>
                                                <label class="upload--squire">
                                                    <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                                    <img class="" id="viewer" src="{{asset('assets/admin/img/upload-en.png')}}" alt="{{ translate('banner image') }}"/>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="input-label" for="exampleFormControlSelect1">
                                                        {{translate('Category')}}
                                                        <span class="input-label-secondary">*</span>
                                                    </label>
                                                    <select name="product_id" class="form-control js-select2-custom" id="catogory_id" required>
                                                        <option selected disabled>Select Category</option>
                                                        @foreach($products as $product)
                                                        <option value="{{$product['id']}}">{{$product['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Modal -->
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="card--header justify-content-between max--sm-grow">
                <h5 class="card-title">{{translate('Item List')}} <span class="badge badge-soft-secondary">{{ $banner->childes->count() }}</span></h5>

            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                @if($banner->section_type == 'cart')
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('#')}}</th>
                            <th class="border-0">{{translate('Product Image')}}</th>
                            <th class="border-0">{{translate('Product name')}}</th>
                            <th class="text-center border-0">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banner->childes as $key => $value)
                        <tr>
                            <td>
                                {{$key+1}}
                            </td>
                            <td>@php($images = json_decode($value->item_detail)->image)
                                <img class="upload--vertical--preview" src="{{ asset(json_decode($images)[0])}}" alt="{{ translate('banner image') }}"
                                    onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-25">
                                    {{json_decode($value->item_detail)->name}}
                                </span>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="banner-{{$value['id']}}"
                                        data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.display.delete.content',[$value['id']])}}" method="post" id="banner-{{$value['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                @elseif($banner->section_type == 'box_section')
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('#')}}</th>
                            <th class="border-0">{{translate('banner image')}}</th>
                            <th class="border-0">{{translate('item_type')}}</th>
                            <th class="border-0">{{translate('Item')}}</th>
                            <th class="border-0">{{translate('priority')}}</th>
                            <th class="text-center border-0">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banner->childes as $key => $value)
                        <tr>
                            <td>
                                {{$key+1}}
                            </td>
                            <td>
                                <div>
                                    <img class="upload--vertical--preview" src="{{ asset($value->attechment )}}" alt="{{ translate('banner image') }}">
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-25">
                                    {{$value['item_type']}}
                                </span>
                            </td>
                            <td>
                                @if($value->item_type == 'product')
                                    @php($images = json_decode($value->item_detail)->image)
                                    <a href="{{route('admin.product.view',[json_decode($value->item_detail)->id])}}" class="product-list-media">
                                        <img class="upload--vertical--preview" src="{{ asset(json_decode($images)[0])}}" alt="{{ translate('banner image') }}"
                                            onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit(json_decode($value->item_detail)->name, 20, $end='...')}}
                                        </h6>
                                    </a>
                                @else
                                    @php($images = json_decode($value->item_detail)->image)
                                    <a href="{{route('admin.category.add')}}" class="product-list-media">
                                    <img src="{{ asset($images)}}" class="img--50" alt="{{ translate('category') }}"
                                            onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit(json_decode($value->item_detail)->name, 20, $end='...')}}
                                        </h6>
                                    </a>
                                    
                                @endif
                            </td>
                            <td>
                                <div class="max-85">
                                    <select name="priority" class="custom-select"
                                        onchange="location.href='{{ route('admin.display.priority', ['id' => $value['id'], 'priority' => '']) }}' + this.value">
                                        @for($i = 0; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ $value->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="banner-{{$value['id']}}"
                                        data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.display.delete.content',[$value['id']])}}" method="post" id="banner-{{$value['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                @else
                    <thead class="thead-light">
                        <tr>
                            <th class="border-0">{{translate('#')}}</th>
                            <th class="border-0">{{translate('banner image')}}</th>
                            <th class="border-0">{{translate('Item')}}</th>
                            <th class="text-center border-0">{{translate('action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banner->childes as $key => $value)
                        <tr>
                            <td>
                                {{$key+1}}
                            </td>
                            <td>
                                <div>
                                    <img class="upload--vertical--preview" src="{{ asset($value->attechment )}}" alt="{{ translate('banner image') }}">
                                </div>
                            </td>
                            <td>@php($images = json_decode($value->item_detail)->image)
                                <a href="{{route('admin.product.view',[json_decode($value->item_detail)->id])}}" class="product-list-media">
                                    @if($banner->ui_type == 'user_product')
                                        <img class="upload--vertical--preview" src="{{ asset(json_decode($images)[0])}}" alt="{{ translate('banner image') }}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                    @else
                                        <img class="upload--vertical--preview" src="{{ asset($images)}}" alt="{{ translate('banner image') }}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                    @endif
                                    <h6 class="name line--limit-2">
                                        {{\Illuminate\Support\Str::limit(json_decode($value->item_detail)->name, 20, $end='...')}}
                                    </h6>
                                </a>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="banner-{{$value['id']}}"
                                        data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.display.delete.content',[$value['id']])}}" method="post" id="banner-{{$value['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                @endif
            </table>
        </div>
        @if(count($banner->childes) == 0)
        <div class="text-center p-4">
            <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
            <p class="mb-0">{{translate('No_data_to_show')}}</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal -->
<!-- <div class="modal fade" id="updatemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('admin.display.update.section')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{ translate('Edit Item') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="d-flex flex-column justify-content-center h-100">
                                <h5 class="text-center mb-3 text--title text-capitalize">
                                    {{translate('banner')}} {{translate('image')}}
                                    <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                                </h5>
                                <label class="upload--vertical">
                                    <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                    <img class="" id="viewer" src="{{asset('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}" />
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="id" id="id">
                        @if ($banner->section_type == 'box_section')
                            <div class="col-md-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="input-label" for="exampleFormControlSelect1">
                                                {{translate('item')}} {{translate('type')}}
                                                <span class="input-label-secondary">*</span>
                                            </label>
                                            <select name="type" id="type" class="form-control show-item">
                                                <option value="product">{{translate('product')}}</option>
                                                <option value="category">{{translate('category')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-0" id="type-product-edit">
                                            <label class="input-label" for="exampleFormControlSelect1">
                                                {{translate('product')}}
                                                <span class="input-label-secondary">*</span>
                                            </label>
                                            <select name="product_id" id="product_id_edit" class="form-control js-select2-custom">
                                                <option selected disabled>Select Product</option>
                                                @foreach($products as $product)
                                                <option value="{{$product['id']}}">{{$product['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group mb-0" id="type-category-edit">
                                            <label class="input-label" for="exampleFormControlSelect1">
                                                {{translate('category')}}
                                                <span class="input-label-secondary">*</span>
                                            </label>
                                            <select name="category_id" id="category_id_edit" class="form-control js-select2-custom">
                                                <option selected disabled>Select Category</option>
                                                @foreach($categories as $category)
                                                <option value="{{$category['id']}}">{{$category['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="col-md-12">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="input-label" for="exampleFormControlSelect1">
                                            {{translate('product')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="product_id" class="form-control js-select2-custom">
                                            <option selected disabled>Select Product</option>
                                            @foreach($products as $product)
                                            <option value="{{$product['id']}}">{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div> -->
@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/banner.js') }}"></script>
<script>
    $('#catogory_id').change(function() {
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
                parent_id: $('#catogory_id').val()
            },
            success: function(data) {
                console.log(data.options);
                console.log(data.option);
                console.log($('#catogory_id').val());
                $('#product_id').html(data.options);
            }
        });
    });
</script>
@endpush