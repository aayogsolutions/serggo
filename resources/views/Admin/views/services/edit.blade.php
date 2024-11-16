@extends('Admin.layouts.app')

@section('title', translate('Update Service'))

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/edit.png')}}" class="w--24" alt="">
            </span>
            <span>
                {{translate('Service')}} {{translate('update')}}
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
    <form action="javascript:void(0)" method="post" id="product_form" enctype="multipart/form-data" class="row g-2">
        @csrf
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body pt-2">
                    <div id="english-form">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                            <input type="text" name="name" value="{{$service['name']}}" class="form-control"
                                placeholder="{{translate('New Service')}}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('short')}}
                                {{translate('description')}}</label>
                            <textarea name="description" class="form-control h--172px summernote"
                                id="hiddenArea">{{ $service['description'] }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <i class="tio-user"></i>
                        </span>
                        <span>
                            {{translate('category')}}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlSelect1">{{translate('category')}}<span
                                        class="input-label-secondary">*</span></label>
                                <select name="category_id" id="get_category" class="form-control js-select2-custom">
                                    @foreach($categories as $category)
                                    <option value="{{$category['id']}}"
                                        {{ $category->id == $service['category_id'] ? 'selected' : ''}}>
                                        {{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlSelect1">{{translate('sub_category')}}<span
                                        class="input-label-secondary"></span></label>
                                <select name="sub_category_id" id="sub-categories"
                                    data-id="{{ $service['sub_category_id'] }}" class="form-control js-select2-custom">
                                    @foreach($subcategories as $subcategory)
                                    <option value="{{$subcategory['id']}}"
                                        {{ $subcategory->id == $service['sub_category_id'] ? 'selected' : ''}}>
                                        {{$subcategory['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlSelect1">{{translate('child_category')}}<span
                                        class="input-label-secondary"></span></label>
                                <select name="sub_category_id" id="sub-categories"
                                    data-id="{{ $service['sub_category_id'] }}" class="form-control js-select2-custom">
                                    @foreach($childcategories as $childcategory)
                                    <option value="{{$childcategory['id']}}"
                                        {{ $childcategory->id == $service['sub_category_id'] ? 'selected' : ''}}>
                                        {{$childcategory['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-2">
                <div class="card min-h-116px">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="d-flex flex-wrap-reverse justify-content-between">
                            <div class="w-200 flex-grow-1 mr-3">
                                {{translate('Turning Visibility off will not show this product in the user app and website')}}
                            </div>
                            <div class="d-flex align-items-center mb-2 mb-sm-0">
                                <h5 class="mb-0 mr-2">{{ translate('Visibility') }}</h5>
                                <label class="toggle-switch my-0">
                                    <input type="checkbox" class="toggle-switch-input" name="status" value=""
                                        {{$service['status']==0?'checked':''}}>
                                    <span class="toggle-switch-label mx-auto text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{translate('service')}} {{translate('image')}} <small class="text-danger">* (
                            {{translate('ratio')}} 1:1 )</small></h5>
                    <div class="product--coba">
                        <div class="row g-2" id="coba">
                            @if (!empty(json_decode($service['image'],true)))
                            @foreach(json_decode($service['image']) as $identification_image)
                            <div class="spartan_item_wrapper position-relative">
                                <img class="img-150 border rounded p-3" src="{{ asset($identification_image)}}"
                                    alt="{{ translate('identity_image') }}">
                                <a href="{{route('admin.service.remove-image',[$service['id'],$identification_image])}}"
                                    class="spartan__close">
                                    <i class="tio-add-to-trash"></i>
                                </a>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <i class="tio-label"></i>
                        </span>
                        <span>
                            {{translate('tags')}}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="tags" placeholder="Enter tags"
                                        value="@foreach(json_decode($service->tags) as $c) {{$c.','}} @endforeach"
                                        data-role="tagsinput">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">
                        <span class="card-header-icon">
                            <i class="tio-dollar"></i>
                        </span>
                        <span>
                            {{translate('price_information')}}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="p-2">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('service_price')}}</label>
                                    <input type="number" value="{{$service['price']}}" min="0" max="100000000"
                                        name="price" class="form-control" step="any"
                                        placeholder="{{ translate('Ex : 100') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlSelect1">{{translate('Time_duration')}}<span
                                            class="input-label-secondary"></span></label>
                                    <select name="time_duration" id="choice_time_duration"
                                        class="form-control js-select2-custom" >
                                            <option value="10_minutes" {{$service['time_duration']=='10_minutes'?'selected':''}}>
                                                {{translate('10 Minutes')}}
                                            </option>
                                            <option value="20_minutes" {{$service['time_duration']=='20_minutes'?'selected':''}}>
                                                {{translate('20 Minutes')}}
                                            </option>
                                            <option value="30_minutes" {{$service['time_duration']=='30_minutes'?'selected':''}}>
                                                {{translate('30 Minutes')}}
                                            </option>
                                            <option value="40_minutes" {{$service['time_duration']=='40_minutes'?'selected':''}}>
                                                {{translate('40 Minutes')}}
                                            </option>
                                            <option value="50_minutes" {{$service['time_duration']=='50_minutes'?'selected':''}}>
                                                {{translate('50 Minutes')}}
                                            </option>
                                            <option value="1_hour" {{$service['time_duration']=='1_hour'?'selected':''}}>
                                                {{translate('1 Hour')}}
                                            </option>
                                            <option value="1_hour_10_minutes" {{$service['time_duration']=='1_hour_10_minutes'?'selected':''}}>
                                                {{translate('1 Hour 10 Minutes')}}
                                            </option>
                                            <option value="1_hour_20_minutes" {{$service['time_duration']=='1_hour_20_minutes'?'selected':''}}>
                                                {{translate('1 Hour 20 Minutes')}}
                                            </option>
                                            <option value="1_hour_30_minutes" {{$service['time_duration']=='1_hour_30_minutes'?'selected':''}}>
                                                {{translate('1 Hour 30 Minutes')}}
                                            </option>
                                            <option value="1_hour_40_minutes" {{$service['time_duration']=='1_hour_40_minutes'?'selected':''}}>
                                                {{translate('1 Hour 40 Minutes')}}
                                            </option>
                                            <option value="1_hour_50_minutes" {{$service['time_duration']=='1_hour_50_minutes'?'selected':''}}>
                                                {{translate('1 Hour 50 Minutes')}}
                                            </option>
                                            <option value="2_hour" {{$service['time_duration']=='2_hour'?'selected':''}}>
                                                {{translate('2 Hour')}}
                                            </option>
                                    </select>
                                </div>
                            </div>  
                           
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('discount')}}
                                        {{translate('type')}}</label>
                                    <select name="discount_type" id="discount_type"
                                        class="form-control js-select2-custom">
                                        <option value="percent" {{$service['discount_type']=='percent'?'selected':''}}>
                                            {{translate('percent')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('discount')}}
                                        <span
                                            id="discount_symbol">{{$service['discount_type']=='amount'?'':'(%)'}}</span></label>
                                    <input type="number" min="0" value="{{$service['discount']}}" max="100000"
                                        name="discount" class="form-control" step="any"
                                        placeholder="{{ translate('Ex : 100') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('tax_type')}}</label>
                                    <select name="tax_type" id="tax_type" class="form-control js-select2-custom">
                                        <option value="Including" {{$service['tax_type']=='Including'?'selected':''}}>
                                            {{translate('Including')}}
                                        </option>
                                        <option value="Excluding" {{$service['tax_type']=='Excluding'?'selected':''}}>
                                            {{translate('Excluding')}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('tax_rate')}}
                                        <span id="tax_symbol">{{$service['tax_type']=='amount'?'':'(%)'}}</span></label>
                                    <input type="number" value="{{$service['tax']}}" min="0" max="100000" name="tax"
                                        class="form-control" step="any" placeholder="{{ translate('Ex : 7') }}"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="col-12">
            <div class="btn--container justify-content-end">
                <button type="reset" class="btn btn--reset">{{translate('clear')}}</button>
                <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
            </div>
        </div>
    </form>
</div>

@endsection


@push('script_2')
<script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('.summernote').summernote({
        height: 200
    });

    $(input['data-role="tagsinput"']).tagsinput();


});

$(document).on('change', '#get_category', function() {

    $.ajax({
        type: "get",
        url: "{{route('admin.service.get-categories')}}",
        data: {
            "_token": "{{ csrf_token() }}",
            parent_id: $(this).val()
        },
        success: function(data) {
            console.log(data.options);
            $('#sub-categories').html(data.options);
        }
    });
});

$(document).on('change', '#sub-categories', function() {

$.ajax({
    type: "get",
    url: "{{route('admin.service.get-categories')}}",
    data: {
        "_token": "{{ csrf_token() }}",
        parent_id: $(this).val()
    },
    success: function(data) {
        console.log(data.options);
        $('#sub-categories').html(data.options);
    }
});
});

</script>
<script>
$(".lang_link").click(function(e) {
    e.preventDefault();
    $(".lang_link").removeClass('active');
    $(".lang_form").addClass('d-none');
    $(this).addClass('active');

    let form_id = this.id;
    let lang = form_id.split("-")[0];
    console.log(lang);
    $("#" + lang + "-form").removeClass('d-none');
    if (lang == 'en') {
        $("#from_part_2").removeClass('d-none');
    } else {
        $("#from_part_2").addClass('d-none');
    }


})
</script>
<script type="text/javascript">
$(function() {
    $("#coba").spartanMultiImagePicker({
        fieldName: 'images[]',
        maxCount: 4,
        rowHeight: '150px',
        groupClassName: '',
        maxFileSize: '',
        placeholderImage: {
            image: "{{asset('/assets/admin/img/upload-en.png')}}",
            width: '100%'
        },
        dropFileLabel: "Drop Here",
        onAddRow: function(index, file) {

        },
        onRenderedPreview: function(index) {

        },
        onRemoveRow: function(index) {

        },
        onExtensionErr: function(index, file) {
            toastr.error('Please only input png or jpg type file', {
                CloseButton: true,
                ProgressBar: true
            });
        },
        onSizeErr: function(index, file) {
            toastr.error('File size too big', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    });
});
</script>

<script>
$(document).on('ready', function() {
    $('.js-select2-custom').each(function() {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});
</script>

<script src="{{asset('assets/admin')}}/js/tags-input.min.js"></script>

<script>
$('#choice_attributes').on('change', function() {
    $('#customer_choice_options').html(null);
    $.each($("#choice_attributes option:selected"), function() {
        add_more_customer_choice_option($(this).val(), $(this).text());
    });
});

function add_more_customer_choice_option(i, name) {
    let n = name.split(' ').join('');
    $('#customer_choice_options').append(
        '<div class="row"><div class="col-md-3"><input type="hidden" name="choice_no[]" value="' + i +
        '"><input type="text" class="form-control" name="choice[]" value="' + n +
        '" placeholder="Choice Title" readonly></div><div class="col-lg-9"><input type="text" class="form-control" name="choice_options_' +
        i +
        '[]" placeholder="Enter choice values" data-role="tagsinput" onchange="combination_update()"></div></div>');
    $("input[data-role=tagsinput], select[multiple][data-role=tagsinput]").tagsinput();
}

function combination_update() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $.ajax({
        type: "POST",
        url: "{{route('admin.product.variant-combination')}}",
        data: $('#product_form').serialize(),
        success: function(data) {
            $('#variant_combination').html(data.view);
            if (data.length > 1) {
                $('#quantity').hide();
            } else {
                $('#quantity').show();
            }
        }
    });
}
</script>

<!-- <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script> -->

<script>
$('#product_form').on('submit', function() {



    var formData = new FormData(this);

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.post({
        url: "{{route('admin.service.update',[$service['id']])}}",
        // data: $('#product_form').serialize(),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        success: function(data) {
            if (data.errors) {
                for (var i = 0; i < data.errors.length; i++) {
                    toastr.error(data.errors[i].message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            } else {
                toastr.success("{{translate('service updated successfully!')}}", {
                    CloseButton: true,
                    ProgressBar: true
                });
                setTimeout(function() {
                    location.href = "{{route('admin.service.list')}}";
                }, 2000);
            }
        }
    });
});
</script>

<script>
$('#discount_type').change(function() {
    if ($('#discount_type').val() == 'percent') {
        $("#discount_symbol").html('(%)')
    } else {
        $("#discount_symbol").html('')
    }
});

$('#tax_type').change(function() {
    if ($('#tax_type').val() == 'percent') {
        $("#tax_symbol").html('(%)')
    } else {
        $("#tax_symbol").html('')
    }
});
</script>

<script>
function update_qty() {
    var total_qty = 0;
    var qty_elements = $('input[name^="stock_"]');
    for (var i = 0; i < qty_elements.length; i++) {
        total_qty += parseInt(qty_elements.eq(i).val());
    }
    if (qty_elements.length > 0) {
        $('input[name="total_stock"]').attr("readonly", true);
        $('input[name="total_stock"]').val(total_qty);
        console.log(total_qty)
    } else {
        $('input[name="total_stock"]').attr("readonly", false);
    }
}
</script>

@endpush