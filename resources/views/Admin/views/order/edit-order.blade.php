@extends('layouts.admin.app')

@section('title', translate('Order Details'))

@push('css_or_js')
<style>
    figure {
        margin-bottom: -1px;
    }

    button:focus,
    input:focus {
        outline: none;
        box-shadow: none;
    }

    a,
    a:hover {
        text-decoration: none;
    }

    /*--------------------------*/
    .qty-container {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-container .input-qty {
        text-align: center;
        padding: 6px 10px;
        border: 1px solid #d4d4d4;
        max-width: 80px;
    }

    .qty-container .qty-btn-minus,
    .qty-container .qty-btn-plus {
        border: 1px solid #d4d4d4;
        padding: 10px 13px;
        font-size: 10px;
        height: 38px;
        width: 38px;
        transition: 0.3s;
    }

    .qty-container .qty-btn-plus {
        margin-left: -1px;
    }

    .qty-container .qty-btn-minus {
        margin-right: -1px;
    }


    /*---------------------------*/
    .btn-cornered,
    .input-cornered {
        border-radius: 4px;
    }

    .btn-rounded {
        border-radius: 50%;
    }

    .input-rounded {
        border-radius: 50px;
    }
</style>
<link rel="stylesheet" href="{{asset('/public/assets/admin/css/lightbox.min.css')}}">
@endpush
@section('content')

<div class="content container-fluid">
    <div class="page-header d-flex justify-content-between">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('public/assets/admin/img/order.png')}}" class="w--20" alt="">
            </span>
            <span>
                {{translate('order details')}}
            </span>
        </h1>
    </div>

    <div class="row" id="printableArea">
        <div class="col-lg-8 order-print-area-left">
            <div class="card mb-3 mb-lg-5">
                <div class="card-header flex-wrap align-items-start border-0">
                    <div class="order-invoice-left">
                        <h5 class="form-label mb-3">
                            <span class="card-header-icon">
                                <i class="tio-shop-outlined"></i>
                            </span>
                            <span>{{translate('Branch information')}}</span>
                        </h5>
                        <div class="media align-items-center deco-none resturant--information-single">
                            <div class="avatar avatar-circle">
                                <img class="avatar-img w-75px" src="{{$order->branch?->imageFullPath}}" alt="{{ translate('Image Description')}}">
                            </div>
                            <div class="media-body">
                                @if(isset($order->branch))
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    {{$order->branch?->name}}
                                </span>
                                <span>{{\App\Model\Order::where('branch_id',$order['branch_id'])->count()}} {{translate('Orders')}}</span>
                                <span class="text--title font-semibold d-block">
                                    <i class="tio-call-talking-quiet mr-2"></i>
                                    <a href="Tel:{{$order->branch?->phone}}">{{$order->branch?->phone}}</a>
                                </span>
                                <span class="text--title">
                                    <i class="tio-email mr-2"></i>
                                    <a href="mailto:{{$order->branch?->email}}">{{$order->branch?->email}}</a>
                                </span>
                                @else
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    {{translate('Branch Deleted')}}
                                </span>
                                @endif
                            </div>
                        </div>
                        @if(isset($order->branch))
                        <hr>
                        <span class="d-block">
                            <a target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{ $order->branch?->latitude}}+{{$order->branch?->longitude }}">
                                <i class="tio-poi"></i> {{ $order->branch?->address}}
                            </a>
                        </span>
                        @endif

                    </div>
                    <div class="order-invoice-right mt-3 mt-sm-0">
                        <h5 class="form-label mb-3">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>{{translate('Customer information')}}</span>
                        </h5>
                        @if($order->is_guest == 1)
                        <div class="media align-items-center deco-none customer--information-single">
                            <div class="avatar avatar-circle">
                                <img class="avatar-img" src="{{asset('public/assets/admin/img/admin.jpg')}}" alt="{{ translate('Image Description')}}">
                            </div>
                            <div class="media-body">
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    {{translate('Guest Customer')}}
                                </span>
                            </div>
                        </div>
                        @else
                        @if($order->user_id == null)
                        <div class="media align-items-center deco-none customer--information-single">
                            <div class="avatar avatar-circle">
                                <img class="avatar-img" src="{{asset('public/assets/admin/img/admin.jpg')}}" alt="{{ translate('Image Description')}}">
                            </div>
                            <div class="media-body">
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    {{translate('Walking Customer')}}
                                </span>
                            </div>
                        </div>
                        @endif
                        @if($order->user_id != null && !isset($order->customer) )
                        <div class="media align-items-center deco-none customer--information-single">
                            <div class="avatar avatar-circle">
                                <img class="avatar-img" src="{{asset('public/assets/admin/img/admin.jpg')}}" alt="{{ translate('Image Description')}}">
                            </div>
                            <div class="media-body">
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    {{translate('Customer_not_available')}}
                                </span>
                            </div>
                        </div>
                        @endif
                        @if(isset($order->customer) )
                        <div class="media align-items-center deco-none customer--information-single">
                            <div class="avatar avatar-circle">
                                <img class="avatar-img" src="{{$order->customer->imageFullPath}}" alt="{{ translate('Image Description')}}">
                            </div>
                            <div class="media-body">
                                <span class="fz--14px text--title font-semibold text-hover-primary d-block">
                                    <a href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                </span>
                                <span>{{\App\Model\Order::where('user_id',$order['user_id'])->count()}} {{translate("orders")}}</span>
                                <span class="text--title font-semibold d-block">
                                    <i class="tio-call-talking-quiet mr-2"></i>
                                    <a href="Tel:{{$order->customer['phone']}}">{{$order->customer['phone']}}</a>
                                </span>
                                <span class="text--title">
                                    <i class="tio-email mr-2"></i>
                                    <a href="mailto:{{$order->customer['email']}}">{{$order->customer['email']}}</a>
                                </span>
                            </div>
                        </div>
                        @endif
                        @endif

                        <hr>

                        <h1 class="page-header-title">
                            <span class="mr-3">{{translate('order ID')}} #{{$order['id']}}</span>
                            <span class="badge badge-soft-info py-2 px-3">{{$order->branch?$order->branch->name:translate('Branch deleted!')}}</span>
                        </h1>
                        <span><i class="tio-date-range"></i>
                            {{date('d M Y',strtotime($order['created_at']))}} {{ date(config('time_format'), strtotime($order['created_at'])) }}
                        </span>

                    </div>
                    @if($order['order_type'] != 'pos')

                    <div class="w-100">
                        <hr>
                        <h6>
                            <strong>{{translate('order')}} {{translate('note')}}</strong>
                            : <span class="text-body"> {{$order['order_note']}} </span>
                        </h6>
                    </div>
                    @endif
                </div>
            </div>
        </div>


        <div class="col-lg-4 order-print-area-right">
            @if($order['order_type'] != 'pos')
            <div class="card">
                <div class="card-body">
                    @if($order['order_type']!='self_pickup')
                    @php($address=\App\Model\CustomerAddress::find($order['delivery_address_id']))

                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>{{translate('delivery information')}}</span>
                        </h5>
                    </div>

                    @if(isset($address))
                    <div class="delivery--information-single flex-column mt-3">
                        <div class="d-flex">
                            <span class="name">
                                {{translate('name')}}
                            </span>
                            <span class="info">{{$address['contact_person_name']}}</span>
                        </div>
                        <div class="d-flex">
                            <span class="name">{{translate('phone')}}</span>
                            <span class="info">{{ $address['contact_person_number']}}</span>
                        </div>
                        @if($address['road'])
                        <div class="d-flex">
                            <span class="name">{{translate('road')}}</span>
                            <span class="info">#{{ $address['road']}}</span>
                        </div>
                        @endif
                        @if($address['house'])
                        <div class="d-flex">
                            <span class="name">{{translate('house')}}</span>
                            <span class="info">#{{ $address['house']}}</span>
                        </div>
                        @endif
                        @if($address['floor'])
                        <div class="d-flex">
                            <span class="name">{{translate('floor')}}</span>
                            <span class="info">#{{ $address['floor']}}</span>
                        </div>
                        @endif
                        <hr class="w-100">
                        <div>
                            <a target="_blank" href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                <i class="tio-poi"></i> {{$address['address']}}
                            </a>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="col-lg-12">
            <form action="{{route('admin.orders.edit_item.submit' , $order['id'])}}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="order_id" value="{{$order['id']}}">
                <div class="card">
                    <div class="card-body">
                        @php($subTotal=0)
                        @php($amount=0)
                        @php($totalTax=0)
                        @php($total_dis_on_pro=0)
                        @php($totalItemDiscount=0)
                        @php($price_after_discount=0)
                        @php($updatedTotalTax=0)
                        @php($vatStatus = '')
                        <div class="table-responsive">
                            <table class="table table-borderless table-nowrap table-align-middle card-table dataTable no-footer mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">{{translate('SL')}}</th>
                                        <th class="border-0">{{translate('Replace')}}</th>
                                        <th class="border-0">{{translate('Item details')}}</th>
                                        <th class="border-0">{{translate('Price')}}</th>
                                        <th class="border-0">{{translate('Discount')}}</th>
                                        <th class="border-0">{{translate('Total Price')}}</th>
                                        <th class="border-0">{{translate('Action')}}</th>
                                    </tr>
                                </thead>
                                @foreach($order->details as $detail)
                                @if($detail->product_details != null)
                                @php($product = json_decode($detail->product_details, true))
                                <tr>
                                    <td>
                                        {{$loop->iteration}}
                                    </td>
                                    <td>
                                        <div class="row">
                                            <div class="col-md-2 col-lg-2 col-sm-3 align-items-center d-flex">
                                                <a class="link action-btn btn--danger btn-outline-danger close_button Replaced-item" style="min-width: 28px;" data-toggle="modal" data-target="#shipping-address-modal-{{$product['id']}}" href="javascript:"><i class="tio-edit"></i></a>
                                            </div>

                                            <div class="col-md-10 col-lg-10 col-sm-9 main-replaced-item-section">
                                                <div class="replaced-item-section">
                                                    <input type='hidden' name='alternate[]' value='0'>
                                                    <input type='hidden' name='alternate_qyt[]' value='0'>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="shipping-address-modal-{{$product['id']}}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-toped" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-top-cover bg-dark text-center">
                                                        <figure class="position-absolute right-0 bottom-0 left-0">
                                                            <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" viewBox="0 0 1920 100.1">
                                                                <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z" />
                                                            </svg>
                                                        </figure>

                                                        <div class="modal-close">
                                                            <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal" aria-label="Close">
                                                                <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                                                    <path fill="currentColor" d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="modal-top-cover-icon">
                                                        <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                                                            <i class="tio-location-search"></i>
                                                        </span>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-3">
                                                            <label for="requiredLabel" class="col-md-5 col-form-label input-label text-md-left">
                                                                {{translate('select Alternate Product')}}
                                                            </label>
                                                            <div class="col-md-12 js-form-message">
                                                                <select name="" id="product_id{{$product['id']}}" class="form-control js-select2-custom mx-1 product_id">
                                                                    <option value="start" selected disabled>{{ translate('Select Product') }}</option>
                                                                    @foreach($all_product as $value)
                                                                    <option value="{{$value['id']}}">{{$value['name']}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-white" data-dismiss="modal">{{translate('close')}}</button>
                                                        <button type="button" class="btn btn-primary Replaced-item-button" data-dismiss="modal">{{translate('save')}} {{translate('changes')}}</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="product[]" value="{{$product['id']}}">
                                        <div class="media media--sm">
                                            <div class="avatar avatar-xl mr-3">
                                                @if($detail->product && $detail->product['image'] != null )
                                                <img class="img-fluid rounded aspect-ratio-1" src="{{ $detail->product->identityImageFullPath[0] }}" alt="{{translate('Image Description')}}">
                                                @else
                                                <img src="{{asset('public/assets/admin/img/160x160/2.png')}}" class="img-fluid rounded aspect-ratio-1">
                                                @endif
                                            </div>
                                            <div class="media-body">
                                                <h5 class="line--limit-1">{{$product['name']}}</h5>
                                                @if(count(json_decode($detail['variation'],true)) > 0)
                                                @foreach(json_decode($detail['variation'],true)[0]??json_decode($detail['variation'],true) as $key1 =>$variation)
                                                <div class="font-size-sm text-body text-capitalize">
                                                    @if($variation != null)
                                                    <span>{{$key1}} : </span>
                                                    @endif
                                                    <span class="font-weight-bold">{{$variation}}</span>
                                                </div>
                                                @endforeach
                                                @endif
                                                <h5 class="mt-1"><span class="text-body">{{translate('Unit')}}</span> : {{$detail['unit']}} </h5>
                                                <h5 class="mt-1"><span class="text-body">{{translate('Unit Price')}}</span> : {{$detail['price']}} </h5>
                                                <h5 class="mt-1"><span class="text-body">{{translate('QTY')}}</span> : {{$detail['quantity']}} </h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <h6>{{ Helpers::set_symbol($detail['price'] * $detail['quantity']) }}</h6>
                                    </td>
                                    <td class="text-center">
                                        <h6>{{ Helpers::set_symbol($detail['discount_on_product'] * $detail['quantity']) }}</h6>
                                    </td>
                                    <td class="text-center">
                                        <h5>{{ Helpers::set_symbol(($detail['price'] * $detail['quantity']) - ($detail['discount_on_product'] * $detail['quantity'])) }}</h5>
                                    </td>
                                    <td class="text-center">
                                        <a class="link action-btn btn--danger btn-outline-danger delete-button" data-id="{{$detail['id']}}" style="min-width: 28px;" href="javascript:">
                                            <i class="tio-delete"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" name="direct" value="true">{{translate('Save')}} {{translate('directly')}}</button>
                        <button type="submit" class="btn btn-primary" name="response" value="true">{{translate('Save')}} {{translate('with customer response')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script src="{{ asset('public/assets/admin/js/flatpicker.js') }}"></script>

<script>
    $(document).ready(function() {


        // var data = 'product';

        // $.ajax({
        //     url:"{{route('admin.product.ProductAjax')}}",
        //     type:'POST',
        //     data:{
        //     "_token": "{{ csrf_token() }}",
        //     data: data
        //     },
        //     success:function(response){
        //     var subdata = [];

        //         if (response == '') {

        //         }else{

        //             $('.product_id').html('');

        //             subdata += "<option value='' selected disabled>{{ translate('Select Product') }}</option>";

        //             response.forEach(element => {
        //                 subdata += "<option value='"+element.id+"'>"+element.name+"</option>";
        //             });

        //             $('.product_id').html(subdata);
        //         }
        //     }
        // })

        $('.Replaced-item-button').click(function() {

            var id = $(this).parents('td').children('.modal').children('.modal-dialog').children('.modal-content').children('.modal-body').children('.row').children('.js-form-message').children('.product_id').val();

            var append_path = $(this).parents('td').children('.row').children('.main-replaced-item-section').children('.replaced-item-section');

            $.ajax({
                url: "{{route('admin.orders.ProductReplaceAjax')}}",
                type: 'POST',
                data: {
                    "_token": "{{ csrf_token() }}",
                    data: id
                },
                success: function(response) {
                    console.log(response);

                    if (response['image'] != null) {
                        var image = "<img class='img-fluid rounded aspect-ratio-1' src='" + response['fullpath'] + "' alt='{{translate('Image Description')}}'>";
                    } else {
                        var image = "<img src='{{asset('public/assets/admin/img/160x160/2.png')}}' class='img-fluid rounded aspect-ratio-1'>";
                    }

                    var product_rlp = "<input type='hidden' name='alternate[]' value='" + response['id'] + "'><div class='media media--sm'><div class='avatar avatar-xl mr-3'>" + image + " </div><div class='media-body'><h5 class='line--limit-1'>" + response['name'] + "</h5>  <h5 class='mt-1'><span class='text-body'>{{translate('Unit')}}</span> : " + response['unit'] + " </h5><h5 class='mt-1'><span class='text-body'>{{translate('Unit Price')}}</span> : " + response['price'] + " </h5><h5 class='mt-1'><span class='text-body'>{{translate('Unit Price')}}</span><div class='qty-container'><button class='qty-btn-minus btn-danger' type='button'><i class='tio-add-alt'></i>-</button><input type='text' name='alternate_qyt[]' value='0' class='input-qty' /><button class='qty-btn-plus btn-danger' type='button'><i class='tio-add'></i></button></div></h5></div></div>";

                    append_path.html('');
                    append_path.html(product_rlp);
                    console.log(append_path);

                    // var subdata = [];

                    // if (response == '') {

                    // }else{

                    //     $('#product_id').html('');

                    //     subdata += "<option value='' selected disabled>{{ translate('Select Product') }}</option>";

                    //     response.forEach(element => {
                    //         subdata += "<option value='"+element.id+"'>"+element.name+"</option>";
                    //     });

                    //     $('#product_id').html(subdata);
                    // }
                }

            })


        })

        // var buttonPlus = $(".qty-btn-plus");
        // var buttonMinus = $(".qty-btn-minus");

        // var incrementPlus = buttonPlus.click(function() {
        //     var $n = $(this)
        //         .parent(".qty-container")
        //         .find(".input-qty");
        //     $n.val(Number($n.val()) + 1);
        // });

        // var incrementMinus = buttonMinus.click(function() {
        //     var $n = $(this)
        //         .parent(".qty-container")
        //         .find(".input-qty");
        //     var amount = Number($n.val());
        //     if (amount > 0) {
        //         $n.val(amount - 1);
        //     }
        // });

        $(document).on('click', ".qty-btn-plus", function(){
            var $n = $(this)
                .parent(".qty-container")
                .find(".input-qty");
            $n.val(Number($n.val()) + 1);
        })

        $(document).on('click', ".qty-btn-minus", function(){
            var $n = $(this)
                .parent(".qty-container")
                .find(".input-qty");
            var amount = Number($n.val());
            if (amount > 0) {
                $n.val(amount - 1);
            }
        })





        $(document).on('click','.delete-button' , function(){
            var deleted_id = $(this).attr('data-id');
            delete_alert('Are You Sure you want to delete this');

            function delete_alert(message) {
                Swal.fire({
                    title: '{{translate("Delete Alert?")}}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#01684b',
                    cancelButtonText: '{{translate("No")}}',
                    confirmButtonText: '{{translate("Yes")}}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url:"{{route('admin.orders.ProductDeleteAjax')}}",
                            type:'POST',
                            data:{
                            "_token": "{{ csrf_token() }}",
                            data: deleted_id
                            },
                            success:function(response){
                                if(response['success'] == true){
                                    toastr.success('{{ translate("Level Deleted successfully!") }}');
                                    location.reload();
                                }
                                
                            }
                        }) 
                    }
                })
            }

            // $.ajax({
            //     url:"{{route('admin.customer.loyalty-point.show_level')}}",
            //     type:'POST',
            //     data:{
            //     "_token": "{{ csrf_token() }}",
            //     data: data
            //     },
            //     success:function(response){
            //     var subdata = [];
                
            //         if (response == '') {
            //             $('#Table-body').html('');
            //             console.log('empty');
                        
            //         }else{

            //             $('#Table-body').html('');
            //             var slno = 1;
            //             response.forEach(element => {
            //                 subdata += "<tr><td class='text-center'>"+slno+"</td><td class='text-center'>"+element.Level+"</td><td class='text-center'>"+element.level_name+"</td><td class='text-center'>"+element.percentage+"</td><td><div class='btn--container justify-content-center'><button class='action-btn' data-toggle='modal' data-target='#exampleModal-"+element.id+"'><i class='tio-edit'></i></i></button><div class='delete-button'  data-id='"+element.id+"'></div></div><div class='modal fade' id='exampleModal-"+element.id+"' tabindex='-1' role='dialog' aria-labelledby='exampleModalLabel' aria-hidden='true'><div class='modal-dialog modal-dialog-centered' role='document'><div class='modal-content'><div class='modal-header'><h5 class='modal-title' id='exampleModalLabel'>Edit "+element.level_name+" Level</h5><button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div><div class='modal-body'><div class='col-lg-12'><div class='card'><div class='card-body pt-2'><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Level_name')}}</label><input type='text' name='level_name' id='level_name'  class='form-control level_name' placeholder='{{translate('level_name')}}' required></div><div class='form-group'><label class='input-label' for='exampleFormControlInput1'>{{translate('Enter_Percentage')}}</label><input type='number' name='percentage' id='percentage percentage' class='form-control' placeholder='{{translate('Percentage')}}' required></div><input type='hidden' name='level_id' id='level_id' class='level_id' value='"+element.id+"' required></div></div></div><div class='col-12'><div class='btn--container justify-content-end'><a href='' class='btn btn--info min-w-120px' data-dismiss='modal'>{{translate('Close')}}</a><button type='submit' class='btn btn--primary'>{{translate('submit')}}</button></div></div></div></div></div></div></td></tr>";

            //                 slno++;
            //             });
                        
            //             $('#Table-body').html(subdata);

            //             $(document).find('tr:last-child').children('td:last-child').children('.btn--container').children('.delete-button').html("<button class='action-btn btn--danger btn-outline-danger close_button'><i class='tio-delete-outlined'></i></button>");
                        
            //         }
            //     }
            // })
        });
    })
</script>

@endpush