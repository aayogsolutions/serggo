@extends('Admin.layouts.app')

@section('title', translate('Approved Product List'))

@section('content')
<div class="content container-fluid product-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/products.png')}}" class="w--24" alt="" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
            </span>
            <span>
                {{ translate(' Rejected product List') }}
                <span class="badge badge-soft-secondary">{{ $products->total() }}</span>
            </span>
        </h1>
    </div>
    <!-- End Page Header -->
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header border-0">
                    <div class="card--header justify-content-end max--sm-grow">
                        <form action="{{url()->current()}}" method="GET" class="mr-sm-auto">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search"
                                    class="form-control"
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
                                <th class="text-center">{{translate('#')}}</th>
                                <th class="text-center">{{translate('product_name')}}</th>
                                <th class="text-center">{{translate('selling_price')}}</th>
                                <th class="text-center">{{translate('vendor info')}}</th>
                                <th class="text-center">{{translate('tax')}}(%)</th>
                                <th class="text-center">{{translate('discount')}}(%)</th>
                                <th class="text-center">{{translate('total_stock')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                       
                            @foreach($products as $key=>$product)
                            <tr>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    {{$products->firstItem()+$key}}
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <a href="javascript:void(0)" class="product-list-media">
                                        @if (!empty(json_decode($product['image'],true)))
                                        <img src="{{ asset(json_decode($product->image)[0])}}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                        @else
                                        <img src="{{asset('assets/admin/img/400x400/img2.jpg')}}">
                                        @endif
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit($product['name'], 20, $end='...')}}
                                        </h6>
                                    </a>
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <div class="max-85 text-right">
                                        {{ Helpers_set_symbol($product['price']) }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="javascript:void(0)" class="product-list-media">
                                        <img class="rounded-full" src="{{ asset($product->vendors->image)}}" alt="{{ translate('vendor') }}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit($product->vendors->name, 20, $end='...')}}
                                        </h6>
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{$product->tax}} %
                                </td>
                                <td class="text-center">
                                    {{$product->discount}} %
                                </td>
                                <td class="text-center">
                                    {{$product->total_stock}}
                                </td>
                                
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <!-- Dropdown -->
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" href="{{route('admin.product.all-view',[$product['id']])}}">
                                            <i class="tio-invisible"></i>
                                        </a>
                                    </div>
                                    <!-- End Dropdown -->
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="page-area">
                        <table>
                            <tfoot class="border-top">
                                {!! $products->links('pagination::bootstrap-4') !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($products)==0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description">
                        <p class="mb-0">{{translate('No_data_to_show')}}</p>
                    </div>
                    @endif
                </div>
                <!-- End Table -->
            </div>
            <!-- End Card -->
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    function status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#107980',
            cancelButtonText: '{{ translate("No") }}',
            confirmButtonText: '{{ translate("Yes") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }

    function featured_status_change_alert(url, message, e) {
        e.preventDefault();
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#107980',
            cancelButtonText: '{{ translate("No") }}',
            confirmButtonText: '{{ translate("Yes") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }

    function daily_needs(id, status) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            url: "{{route('admin.product.daily-needs')}}",
            method: 'POST',
            data: {
                id: id,
                status: status
            },
            success: function() {
                toastr.success('{{ translate("Daily need status updated successfully") }}');
            }
        });
    }
</script>
@endpush