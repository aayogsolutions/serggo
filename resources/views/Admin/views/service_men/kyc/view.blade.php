@extends('Admin.layouts.app')

@section('title', translate('Vendor Details'))

@push('css')
    <style>
        .kyc_main_image{
            width: 350px;
            height: 250px;
            margin: auto;
            object-fit: cover;
            border-radius: 10px;
        }

        #myImg {
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        #myImg:hover {opacity: 0.7;}

        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 2000; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* Add Animation */
        .modal-content, #caption {    
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {-webkit-transform: scale(0)} 
            to {-webkit-transform: scale(1)}
        }

        @keyframes zoom {
            from {transform: scale(0.1)} 
            to {transform: scale(1)}
        }

        /* The Close Button */
        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            transition: 0.3s;
        }

        .close:hover,
        .close:focus {
            color: #bbb;
            text-decoration: none;
            cursor: pointer;
        }

        /* 100% Image Width on Smaller Screens */
        @media only screen and (max-width: 700px){
            .modal-content {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="d-print-none pb-2">
            <div class="page-header border-bottom">
                <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/employee.png')}}" class="w--20" alt="{{ translate('vendor') }}">
                </span>
                    <span class="page-header-title pt-2">
                        {{translate('Partner_Details')}}
                    </span>
                </h1>
            </div>
        </div>

        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-auto mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{translate('partner')}} {{translate('id')}} #{{$vendor['id']}}</h1>
                    <span class="d-block">
                        <i class="tio-date-range"></i> {{translate('joined_at')}} : {{date('d M Y '.config('timeformat'),strtotime($vendor['created_at']))}}
                    </span>
                </div>
            </div>
        </div>
        <div class="row mb-2 g-2">


            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card bg--2">
                    <!-- <img class="resturant-icon" src="{{asset('assets/admin/img/dashboard/1.png')}}" alt="{{ translate('image') }}"> -->
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('aadhar_no')}} : <span>{{ $vendor->aadhar_no }}</span>
                    </div>
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('category')}} : <span>@foreach(json_decode($vendor->category) as $key => $categories) {{ $key == 0 ? $categories : ', '. $categories }} @endforeach</span>
                    </div>
                    
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('delivery_type')}} : <span>{{ $vendor->delivery_type }}</span>
                    </div>
                </div>
            </div>
            
            <!-- <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="resturant-card bg--3">
                    <img class="resturant-icon" src="{{asset('assets/admin/img/dashboard/3.png')}}" alt="{{ translate('image') }}">
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('business_name')}} : <span>{{ $vendor->business_name }}</span>
                    </div>
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('gst_no')}} : <span>{{ $vendor->gst_no }}</span>
                    </div>
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('timing')}} : <span>{{ Carbon\Carbon::parse($vendor->open_time)->format('h:i A') }} - {{ Carbon\Carbon::parse($vendor->close_time)->format('h:i A') }}</span>
                    </div>
                    <div class="for-card-text font-weight-bold  text-uppercase mb-1">
                        {{translate('address')}} : <span>{{ $vendor->address }}</span>
                    </div>
                </div>
            </div> -->
        </div>


        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <div class="row">
                    @foreach(json_decode($vendor->aadhar_document) as $key => $images)
                        <div class="col-md-6 kyc_image mt-2" id="kyc_image-{{$key}} myImg">
                            <label for="" style="font-weight: bold;">
                                {{ translate($key) }}
                            </label>
                            <img class="kyc_main_image" src="{{asset($images)}}" alt="{{ translate('image') }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-header-title">
                            <span class="card-header-icon">
                                <i class="tio-user"></i>
                            </span>
                            <span>
                                @if($vendor)
                                    {{$vendor['name']}}
                                    @else
                                    {{ translate('vendor') }}
                                @endif
                            </span>
                        </h4>
                    </div>
                  
                    @if($vendor)
                        <div class="card-body">
                            <div class="media align-items-center customer--information-single" href="javascript:">
                                <div class="avatar avatar-circle">
                                    <img class="avatar-img" src="{{asset($vendor->image)}}" alt="{{ translate('vendor') }}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                                </div>
                                <div class="media-body">
                                    <ul class="list-unstyled m-0">
                                        <li class="pb-1">
                                            <i class="tio-email mr-2"></i>
                                            <a href="mailto:{{$vendor['email']}}">{{$vendor['email']}}</a>
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-call-talking-quiet mr-2"></i>
                                            <a href="Tel:{{$vendor['number']}}">{{$vendor['number']}}</a>
                                        </li>
                                        <li class="pb-1">
                                            <i class="tio-calendar mr-2"></i>
                                            {{Carbon\Carbon::parse($vendor->dob)->format('d M Y')}}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <hr>
                        </div>
                    @endif
                    <div class="card-footer">
                        <form action="{{ route('admin.sevice_men.kyc.store', $vendor->id) }}" method="post">
                            @csrf
                            <button type="submit" class="btn btn-block btn-primary" name="status" value="2">
                                {{ translate('Save') }}
                            </button>
                            <button type="button" class="btn btn-block btn-danger" data-toggle="modal" data-target="#staticBackdrop">
                                {{ translate('Reject') }}
                            </button>

                            <!-- Modal -->
                            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">
                                                Reject Reason
                                            </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <label for="">
                                                Reason
                                                <span class="text-danger">*</span>
                                            </label>
                                            <textarea name="reject_reason" class="form-control" id="" cols="30"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                Close
                                            </button>
                                            <button type="submit" class="btn btn-primary" name="status" value="3">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function(){
        $('.kyc_image').click(function(){
            var image = $(this).children('img').attr('src');
            console.log(image);
            $('#img01').attr('src',image);
            $('#myModal').modal('show');
        });

        $('.close').click(function(){
            $('#myModal').modal('hide');
        })
    })
</script>
@endpush
