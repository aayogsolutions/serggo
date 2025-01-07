@extends('Admin.layouts.app')

@section('title', translate('Edit Plan'))    

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
                {{translate('Edit Plan')}}   
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
                            <input type="text" name="name" value="{{$plan['name']}}" class="form-control" placeholder="{{translate('New Plan')}}" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="input-label" for="exampleFormControlInput1">
                                {{translate('short')}} {{translate('description')}} (EN)
                            </label>
                            <textarea name="description" class="form-control h--172px summernote" id="hiddenArea">{{$plan['description']}}</textarea>
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
                            {{translate('Services')}}
                        </span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group __select-tag" >
                                <label class="input-label" for="exampleFormControlSelect1">
                                    {{translate('Select service')}}
                                </label>
                                <select name="service" id="choice_service" class="form-control" multiple="multiple">
                                    @foreach($services as $service)
                                    <option value="{{$service['id']}}" {{ in_array($service['id'], $planservicesids) ? 'selected' : '' }}>{{$service['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="customer_choice_service">
                        @foreach($planservices as $service)
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="">
                                        Selected service
                                    </label>
                                    <input type="text" class="form-control" value="{{json_decode($service['service_details'])->name}}" readonly>
                                    <input type="number" name="service[]" class="form-control" value="{{$service['service_id']}}" hidden>
                                </div>
                                <div class="col-md-4">
                                    <label for="">
                                        Quantity
                                    </label>
                                    <input type="number" name="quantity[]" class="form-control" value="{{$service['quantity']}}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">{{translate('Plan')}} {{translate('image')}} 
                        <small class="text-danger">* ({{translate('ratio')}} 1:1 )</small>
                    </h5>
                    <div class="product--coba">
                        <div class="row g-2" id="coba">
                            @if (!empty(json_decode($plan['image'],true)))
                                @foreach(json_decode($plan['image']) as $identification_image)
                                    <div class="spartan_item_wrapper position-relative">
                                        <img class="img-150 border rounded p-3"  src="{{ asset($identification_image)}}" alt="{{ translate('identity_image') }}">
                                        <a href="{{route('admin.amc.plan.remove-image',[$plan['id'],$identification_image])}}" class="spartan__close">
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
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('price')}}
                                    </label>
                                    <input type="number" min="0" max="100000000" step="any" value="{{ $plan['price'] }}" name="price" class="form-control" placeholder="{{ translate('Ex : 349') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('Duration')}}
                                    </label>
                                    <select name="duration" id="duration" class="form-control js-select2-custom">
                                        <option value selected disabled>{{translate('Select_Duration')}}</option>
                                        <option value="1_month" {{ $plan['duration'] == '1_month' ? 'selected' : '' }}>{{translate('1_month')}}</option>
                                        <option value="2_month" {{ $plan['duration'] == '2_month' ? 'selected' : '' }}>{{translate('2_month')}}</option>
                                        <option value="3_month" {{ $plan['duration'] == '3_month' ? 'selected' : '' }}>{{translate('3_month')}}</option>
                                        <option value="4_month" {{ $plan['duration'] == '4_month' ? 'selected' : '' }}>{{translate('4_month')}}</option>
                                        <option value="5_month" {{ $plan['duration'] == '5_month' ? 'selected' : '' }}>{{translate('5_month')}}</option>
                                        <option value="6_month" {{ $plan['duration'] == '6_month' ? 'selected' : '' }}>{{translate('6_month')}}</option>
                                        <option value="7_month" {{ $plan['duration'] == '7_month' ? 'selected' : '' }}>{{translate('7_month')}}</option>
                                        <option value="8_month" {{ $plan['duration'] == '8_month' ? 'selected' : '' }}>{{translate('8_month')}}</option>
                                        <option value="9_month" {{ $plan['duration'] == '9_month' ? 'selected' : '' }}>{{translate('9_month')}}</option>
                                        <option value="10_month" {{ $plan['duration'] == '10_month' ? 'selected' : '' }}>{{translate('10_month')}}</option>
                                        <option value="11_month" {{ $plan['duration'] == '11_month' ? 'selected' : '' }}>{{translate('11_month')}}</option>
                                        <option value="12_month" {{ $plan['duration'] == '12_month' ? 'selected' : '' }}>{{translate('12_month')}}</option>
                                        <option value="13_month" {{ $plan['duration'] == '13_month' ? 'selected' : '' }}>{{translate('13_month')}}</option>
                                        <option value="14_month" {{ $plan['duration'] == '14_month' ? 'selected' : '' }}>{{translate('14_month')}}</option>
                                        <option value="15_month" {{ $plan['duration'] == '15_month' ? 'selected' : '' }}>{{translate('15_month')}}</option>
                                        <option value="16_month" {{ $plan['duration'] == '16_month' ? 'selected' : '' }}>{{translate('16_month')}}</option>
                                        <option value="17_month" {{ $plan['duration'] == '17_month' ? 'selected' : '' }}>{{translate('17_month')}}</option>
                                        <option value="18_month" {{ $plan['duration'] == '18_month' ? 'selected' : '' }}>{{translate('18_month')}}</option>
                                        <option value="19_month" {{ $plan['duration'] == '19_month' ? 'selected' : '' }}>{{translate('19_month')}}</option>
                                        <option value="20_month" {{ $plan['duration'] == '20_month' ? 'selected' : '' }}>{{translate('20_month')}}</option>
                                        <option value="21_month" {{ $plan['duration'] == '21_month' ? 'selected' : '' }}>{{translate('21_month')}}</option>
                                        <option value="22_month" {{ $plan['duration'] == '22_month' ? 'selected' : '' }}>{{translate('22_month')}}</option>
                                        <option value="23_month" {{ $plan['duration'] == '23_month' ? 'selected' : '' }}>{{translate('23_month')}}</option>
                                        <option value="24_month" {{ $plan['duration'] == '24_month' ? 'selected' : '' }}>{{translate('24_month')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('discount_type')}}
                                    </label>
                                    <select name="discount_type" id="discount_type" class="form-control js-select2-custom">
                                        <option value="percent" selected>{{translate('percent')}}</option>
                                        <!-- <option value="amount">{{translate('amount')}}</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('discount')}}
                                        <span id="discount_symbol">(%)</span>
                                    </label>
                                    <input type="number" min="0" max="100000" value="{{ $plan['discount'] }}" name="discount" step="any" id="discount" class="form-control" placeholder="{{ translate('Ex : 5%') }}" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('tax_type')}}
                                    </label>
                                    <select name="tax_type" id="tax_type" class="form-control js-select2-custom">
                                        <option value="percent" selected>{{translate('percent')}}</option>
                                        <!-- <option value="amount">{{translate('amount')}}</option> -->
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('tax_rate')}}
                                        <span id="tax_symbol">(%)</span>
                                    </label>
                                    <input type="number" min="0" value="{{ $plan['tax'] }}" step="0.01" max="100000" name="tax" class="form-control" placeholder="{{ translate('Ex : $ 100') }}" required>
                                </div>
                            </div>
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
            url: "{{route('admin.amc.plan.update',$plan->id)}}",
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
                        location.href = "{{route('admin.amc.plan.list')}}";
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
            maxCount: 1,
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
$(document).on('ready', function() 
{
    $('#choice_service').change(function() 
    { 
        var html = '';
        $("#choice_service :selected").each(function (i,sel) 
        {
            html += `<div class="row">
                        <div class="col-md-8">
                            <label for="">
                                Selected service
                            </label>
                            <input type="text" class="form-control" value="`+ $(sel).text() +`" readonly>
                            <input type="number" name="service[]" class="form-control" value="`+ $(sel).val() +`" hidden>
                        </div>
                        <div class="col-md-4">
                            <label for="">
                                Quantity
                            </label>
                            <input type="number" name="quantity[]" class="form-control" value="1">
                        </div>
                    </div>`;
        });
        $('#customer_choice_service').html(html);
        console.log(html);
        // var current_service = [];
        // $("#choice_service :selected").each(function (i,sel) 
        // {
        //     if(servicedata.includes($(sel).text()))
        //     {
        //         console.log('already added');
        //     }
        //     else
        //     {
        //         console.log('added');
        //     }
        //     current_service.push($(sel).text());
        // });
        // servicedata = current_service;
        // console.log(servicedata);
    });

    $('.js-select2-custom').each(function() {
        var select2 = $.HSCore.components.HSSelect2.init($(this));
    });
});
</script>

<script src="{{asset('assets/admin')}}/js/tags-input.min.js"></script>

@endpush