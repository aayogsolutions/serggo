@extends('Admin.layouts.app')

@section('title', translate('Add new banner'))

@push('css')
    <style>
        .upload--vertical--preview{
            height: 50px;
        }

        #user_service{
            display: none;
        }

        #user_product{
            display: none;
        }

        .table-responsive{
            max-height: 1500px !important;
        }

        .image-section{
            display: flex;
            justify-content: center;
            align-items: center;
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
                    {{translate('banner setup')}}
                </span>
            </h1>
        </div>
        <div class="card mb-3">
            <div class="card-body">
            <form action="{{route('admin.banners.homeslider.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                    <input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">
                                        {{translate('UI')}} {{translate('type')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="type" class="form-control" id="ui_type">
                                        <option selected disabled>{{translate('Select UI type')}}</option>
                                        <option value="user_product">{{translate('user_product')}}</option>
                                        <option value="user_service">{{translate('user_service')}}</option>
                                        <option value="amc">{{translate('AMC')}}</option>
                                        <option value="vendor">{{translate('Vendor')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12" id="user_product">
                                <dov class="row">
                                    <div class="col-12">
                                        <div class="form-group mb-2">
                                            <label class="input-label" for="exampleFormControlSelect1">
                                                {{translate('item')}} {{translate('type')}}
                                                <span class="input-label-secondary">*</span>
                                                </label>
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
                                </dov>
                            </div>

                            <div class="col-12" id="user_service">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">
                                        {{translate('Category')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="category_id" class="form-control js-select2-custom" id="service_catogory_id">
                                        <option selected disabled>Select Category</option>
                                        @foreach($servicecategories as $servicecategory)
                                            <option value="{{$servicecategory['id']}}">{{$servicecategory['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">
                                        {{translate('Sub Category')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="sub_category_id" class="form-control js-select2-custom" id="service_sub_catogory_id">
                                        <option selected disabled>Select Category</option>
                                    </select>
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
                                <img class="" id="viewer" src="{{asset('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}" />
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
            </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header border-0">
                <div class="card--header justify-content-between max--sm-grow">
                    <h5 class="card-title">{{translate('Banner List')}} <span class="badge badge-soft-secondary">{{ $banners->total() }}</span></h5>
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control"
                                placeholder="{{translate('Search_by_ID_or_name')}}" aria-label="Search"
                                value="{{$search}}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">
                                    {{translate('search')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                    <th class="border-0">{{translate('#')}}</th>
                        <th class="border-0">{{translate('banner image')}}</th>
                        <th class="border-0">{{translate('title')}}</th>
                        <th class="border-0">{{translate('UI type')}}</th>
                        <th class="border-0">{{translate('Item')}}</th>
                        <th class="text-center border-0">{{translate('status')}}</th>
                        <th class="text-center border-0">{{translate('priority')}}</th>
                        <th class="text-center border-0">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($banners as $key => $banner)
                        <tr>
                            <td>
                                {{$key+1}}
                            </td>
                            <td>
                                <div class="image-section">
                                    <img class="upload--vertical--preview" src="{{ asset($banner->attechment )}}" alt="{{ translate('banner image') }}" onerror="this.src='{{asset('assets/admin/img/upload-vertical.png')}}'">
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-25">
                                    {{$banner['title']}}
                                </span>
                            </td>
                            <td>
                                {{ translate($banner->ui_type) }}
                            </td>
                            <td>
                                @if($banner->ui_type != 'amc' && $banner->ui_type != 'vendor')
                                    Item Type :- {{ translate($banner->item_type) }}
                                    <br>
                                    Item Name :- {{ translate(json_decode($banner->item_detail)->name) }}
                                @endif
                            </td>
                            <td>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox"
                                        class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $banner->id }}"
                                        data-route="{{ route('admin.banners.homeslider.status', [$banner->id, $banner->status == 1 ? 0 : 1]) }}"
                                        data-message="{{ $banner->status? translate('you_want_to_disable_this_banner'): translate('you_want_to_active_this_banner') }}"
                                        {{ $banner->status == 0 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>
                            <td>
                                <div class="max-85">
                                    <select name="priority" class="custom-select"
                                        onchange="location.href='{{ route('admin.banners.homeslider.priority', ['id' => $banner['id'], 'priority' => '']) }}' + this.value">
                                        @for($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}" {{ $banner->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn" href="{{route('admin.banners.homeslider.edit',[$banner['id']])}}">
                                        <i class="tio-edit"></i>
                                    </a>
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="banner-{{$banner['id']}}"
                                        data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.banners.homeslider.delete',[$banner['id']])}}" method="post" id="banner-{{$banner['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <table>
                <tfoot>
                    {!! $banners->links('pagination::bootstrap-4') !!}
                </tfoot>
            </table>


            </div>
            @if(count($banners) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
            @endif
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
