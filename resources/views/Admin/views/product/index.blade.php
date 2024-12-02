@extends('Admin.layouts.app')

@section('title', translate('Add new product'))

@push('css')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="{{asset('assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/add-product.png')}}" class="w--24" alt="" >
            </span>
            <span>
                {{translate('add New Product')}}
            </span>
        </h1>
    </div>

    <form action="javascript:void(0)" method="post" id="product_form" enctype="multipart/form-data" class="row g-2">
        @csrf
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body pt-2">
                    <div id="-form">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="{{translate('New Product')}}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="input-label" for="exampleFormControlInput1">
                                {{translate('short')}} {{translate('description')}} (EN)
                            </label>
                            <textarea name="description" class="form-control h--172px summernote" id="hiddenArea"></textarea>
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
                                <label class="input-label" for="exampleFormControlSelect1">
                                    {{translate('category')}}
                                    <span class="input-label-secondary">*</span>
                                </label>
                                <select name="category_id" class="form-control js-select2-custom" id="get_category">
                                    <option value="">---{{translate('select')}}---</option>
                                    @foreach($categories as $category)
                                    <option value="{{$category['id']}}">{{$category['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">
                                    {{translate('sub_category')}}
                                    <span class="input-label-secondary"></span>
                                </label>
                                <select name="sub_category_id" id="sub-categories" class="form-control js-select2-custom"></select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{translate('unit')}}</label>
                                <select name="unit" class="form-control js-select2-custom">
                                    <option value="kg">{{translate('kg')}}</option>
                                    <option value="gm">{{translate('gm')}}</option>
                                    <option value="ltr">{{translate('ltr')}}</option>
                                    <option value="pc">{{translate('pc')}}</option>
                                    <option value="ml">{{translate('ml')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 d-none" id="installation-inputs">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{translate('Installation')}}</label>
                                <select name="installation" class="form-control js-select2-custom" id="selectedinstallation">
                                    <option value="none">---{{translate('select')}}---</option>
                                    @foreach($Installations as $value)
                                        <option value="{{$value['id']}}" data-name="{{$value['name']}}">
                                            {{translate(Str::limit($value['installation_name'], $limit = 20, $end = '...'))}} ● {{translate(Helpers_set_symbol($value['installation_charges']))}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{translate('brands')}}</label>
                                <select name="brand" class="form-control js-select2-custom" id="selectbrand">
                                    <option value="">---{{translate('select')}}---</option>
                                    @foreach($brand as $value)
                                    <option value="{{$value['id']}}" data-name="{{$value['name']}}">
                                        {{translate($value['name'])}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6 d-none" id="otherbrandsection">
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{translate('specify')}}</label>
                                <input type="text" class="form-control" name="otherbrand">
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
                                    <input type="checkbox" class="toggle-switch-input" name="status" value="0" checked>
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
                    <h5 class="mb-3">{{translate('product')}} {{translate('image')}} 
                        <small class="text-danger">* ({{translate('ratio')}} 1:1 )</small>
                    </h5>
                    <div class="product--coba">
                        <div class="row g-2" id="coba"></div>
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
                            <div class="form-group __select-tag" >
                                    <label class="input-label" 
                                        for="exampleFormControlSelect1">{{translate('Select tag')}}<span
                                            class="input-label-secondary"></span></label>
                                    <select name="tag_name[]" id="choice_tags" class="form-control" multiple="multiple">
                                        @foreach(\App\Models\Tag::orderBy('name')->get() as $tag)
                                        <option value="{{$tag['name']}}">{{$tag['name']}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                    <!-- <label class="input-label" for="exampleFormControlInput1">{{translate('default_unit_price')}}</label> -->
                                    <input type="number" min="0" max="100000000" step="any" value="0" name="price"
                                        class="form-control" placeholder="{{ translate('Ex : 349') }}" required hidden>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <!-- <label class="input-label" for="exampleFormControlInput1">{{translate('product_stock')}}</label> -->
                                    <input type="number" min="0" max="100000000" value="0" name="total_stock"
                                        class="form-control" placeholder="{{ translate('Ex : 100') }}" hidden>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('discount_type')}}</label>
                                    <select name="discount_type" id="discount_type"
                                        class="form-control js-select2-custom">
                                        <option value="percent" selected>{{translate('percent')}}</option>
                                        <!-- <option value="amount">{{translate('amount')}}</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('discount')}}
                                        <span id="discount_symbol">(%)</span></label>
                                    <input type="number" min="0" max="100000" value="0" name="discount" step="any"
                                        id="discount" class="form-control" placeholder="{{ translate('Ex : 5%') }}"
                                        required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('tax_type')}}</label>
                                    <select name="tax_type" id="tax_type" class="form-control js-select2-custom">
                                        <option value="percent" selected>{{translate('percent')}}</option>
                                        <!-- <option value="amount">{{translate('amount')}}</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('tax_rate')}}
                                        <span id="tax_symbol">(%)</span></label>
                                    <input type="number" min="0" value="0" step="0.01" max="100000" name="tax"
                                        class="form-control" placeholder="{{ translate('Ex : $ 100') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label"
                                        for="exampleFormControlInput1">{{translate('Advance')}}</label>
                                    <select name="advance_status" id="advance_status" class="form-control js-select2-custom">
                                        <option value="1" selected>{{translate('not_applicable')}}</option>
                                        <option value="0">{{translate('applicable')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('advance_rate')}}
                                        <span id="tax_symbol">(%)</span></label>
                                    <input type="text" min="0" value="0" step="0.01" max="100000" name="advance"
                                        class="form-control" placeholder="{{ translate('Ex : $ 100') }}" required>
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
                            <i class="tio-puzzle"></i>
                        </span>
                        <span>
                            {{translate('attribute')}}
                        </span>
                    </h5>
                </div>
                <div class="card-body pb-0">
                    <div class="form-group __select-attr">
                        <label class="input-label"
                            for="exampleFormControlSelect1">{{translate('Select attribute')}}<span
                                class="input-label-secondary"></span></label>
                        <select name="attribute_id[]" id="choice_attributes" class="form-control js-select2-custom"
                            multiple="multiple">
                            @foreach(\App\Models\Attributes::orderBy('name')->get() as $attribute)
                            <option value="{{$attribute['id']}}">{{$attribute['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="customer_choice_options" id="customer_choice_options"></div>
                        </div>
                        <div class="col-md-12">
                            <div class="variant_combination" id="variant_combination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="btn--container justify-content-end">
                <a href="" onclick="location.reload()" class="btn btn--reset min-w-120px">{{translate('reset')}}</a>
                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('script')
<script src="{{asset('assets/admin/js/spartan-multi-image-picker.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $('#sub-categories').change(function() {
        var category_id = $(this).val();

        if($('option:selected', this).attr('data-id') == '0') 
        {
            $('#installation-inputs').removeClass('d-none');
            $('#selectedinstallation').val('none').change();
        }
        else
        {
            $('#installation-inputs').addClass('d-none');
            $('#selectedinstallation').val('none').change();
        }
    });

    $('.summernote').summernote({
        height: 200,
    });

    $('#product_form').on('submit', function() {
        var formData = new FormData(this);
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.post({
            url: "{{route('admin.product.store')}}",
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
                    toastr.success('{{ translate("product uploaded successfully!") }}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                    setTimeout(function() {
                        location.href = "{{route('admin.product.list')}}";
                    }, 2000);
                }
            }
        });
    });
});
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
            image: "{{asset('assets/admin/img/upload-en.png')}}",
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
            toastr.error('{{ translate("Please only input png or jpg type file") }}', {
                CloseButton: true,
                ProgressBar: true
            });
        },
        onSizeErr: function(index, file) {
            toastr.error('{{ translate("File size too big") }}', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    });
});
</script>

<script>
function getRequest(route, id) {
    $.get({
        url: route,
        dataType: 'json',
        success: function(data) {
            $('#' + id).empty().append(data.options);
        },
    });
}
</script>

<script>
$(document).on('ready', function() {
    $('#selectbrand').change(function() {
        // Get the selected option element
        var selectedOption = $("#selectbrand :selected");

        // Get the value of the 'data-name' attribute
        var dataname = selectedOption.attr("data-name");

        console.log(dataname);

        if (dataname == 'other') {
            $('#otherbrandsection').removeClass("d-none");
        } else {
            $('#otherbrandsection').addClass("d-none");
        }
        // var content = $(this).val();
        // $('#other').val(content);
        // console.log();
    });

    $('.js-select2-custom').each(function() {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });

    $('#get_category').change(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "get",
            url: "{{route('admin.product.get-categories')}}",
            contentType: false,
            data: {
                parent_id: $('#get_category').val()
            },
            success: function(data) {
                // console.log(data.options);
                // console.log(data.option);
                // console.log($('#get_category').val());
                $('#sub-categories').html(data.options);
            }
        });
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
        '<div class="row g-1"><div class="col-md-3 col-sm-4"><input type="hidden" name="choice_no[]" value="' + i +
        '"><input type="text" class="form-control" name="choice[]" value="' + n +
        '" placeholder="Choice Title" readonly></div><div class="col-lg-9 col-sm-8"><input type="text" class="form-control" name="choice_options_' +
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
<!-- 
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
var quill = new Quill('#editor', {
    theme: 'snow'
});

$('#product_form').on('submit', function() {

    var myEditor = document.querySelector('#editor')
    $("#hiddenArea").val(myEditor.children[0].innerHTML);

    var formData = new FormData(this);
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.post({
        url: "{{route('admin.product.store')}}",
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
                
                setTimeout(function() {
                    location.href = "{{route('admin.product.list')}}";
                }, 2000);
            }
        }
    });
});
</script> -->

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
$('#distributed_type').change(function() {
    if ($('#distributed_type').val() == 'percent') {
        $("#distributed_symbol").html('(%)')
    } else {
        $("#distributed_symbol").html('')
    }
});
</script>

@endpush