@extends('Admin.layouts.app')

@section('title', translate('service_men List'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/employee.png')}}" class="w--20" alt="{{ translate('vendor') }}">
            </span>
            <span>
                {{translate('service_men list')}} 
                <span class="badge badge-soft-primary ml-2 badge-pill">{{ $vendors->total() }}</span>
            </span>
        </h1>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card--header">
                <form action="{{url()->current()}}" method="GET">
                    <div class="input-group">
                        <input id="datatableSearch_" type="search" name="search" class="form-control"
                            placeholder="{{translate('Search by Name or Phone or Email')}}" aria-label="Search"
                            value="{{$search}}" required autocomplete="off">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text">
                                {{ translate('search') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-hover table-align-middle m-0 text-14px">
                <thead class="thead-light">
                    <tr class="word-nobreak">
                        <th>
                            {{translate('#')}}
                        </th>
                        <th class="table-column-pl-0">{{translate('service_men name')}}</th>
                        <th>{{translate('contact info')}}</th>
                        <th class="">{{translate('Category')}}</th>
                        <th class="">{{translate('delivery_type')}}</th>
                        <th class="text-center">{{translate('action')}}</th>

                    </tr>
                </thead>
                <tbody id="set-rows">
                    @foreach($vendors as $key => $vendor)
                    <tr>
                        <td>
                            {{$vendors->firstItem() + $key}}
                        </td>
                        <td class="table-column-pl-0">
                            <a href="#" class="product-list-media">
                                <img class="rounded-full" src="{{ asset($vendor->image)}}" alt="{{ translate('vendor') }}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                                <div class="table--media-body">
                                    <h5 class="title m-0">
                                        {{$vendor['name']}}
                                    </h5>
                                </div>
                            </a>
                        </td>
                        <td>
                            <h5 class="m-0">
                                <a href="mailto:{{$vendor['email']}}">{{$vendor['email']}}</a>
                            </h5>
                            <div>
                                <a href="Tel:{{$vendor['phone']}}">{{$vendor['number']}}</a>
                            </div>
                        </td>
                        <td>
                            @foreach(json_decode($vendor->category) as $tag)
                                <span class="badge badge-soft-primary mr-2">{{$tag}}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge badge-soft-primary mr-2">{{$vendor['delivery_type']}}</span>
                        </td>
                        <td class="btn--container justify-content-center">
                            <a class="action-btn" href="{{route('admin.service_men.kyc.view',[$vendor['id']])}}">
                                <i class="tio-invisible"></i>
                            </a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($vendors) == 0)
        <div class="text-center p-4">
            <img class="w-120px mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}"
                alt="{{ translate('image') }}">
            <p class="mb-0">{{translate('No_data_to_show')}}</p>
        </div>
        @endif

        <div class="card-footer">
            {!! $vendors->links('pagination::bootstrap-4') !!}
        </div>

    </div>
</div>
@endsection