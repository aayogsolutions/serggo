@extends('Admin.layouts.app')

@section('title', translate('Product Approval List'))

@section('content')
<div class="content container-fluid product-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/products.png')}}" class="w--24" alt="" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
            </span>
            <span>
                {{ translate('product Approval List') }}
                <span class="badge badge-soft-secondary">{{ $vendors->total() }}</span>
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
                                <th class="text-center">{{translate('vender_info')}}</th>
                                <th class="">{{translate('approved_products')}}</th>
                                <th class="text-center">{{translate('joined_at')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                       
                            @foreach($vendors as $key=>$vendor)
                            <tr>
                                <td class="pt-1 pb-3 text-center {{$key == 0 ? 'pt-4' : '' }}">
                                    {{1+$key}}
                                </td>
                                <td class="pt-1 pb-3 {{$key == 0 ? 'pt-4' : '' }}">
                                    <a href="javascript:void(0)" class="product-list-media">
                                        <img class="rounded-full" src="{{ asset($vendor->image)}}" alt="{{ translate('vendor') }}" onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit($vendor->name, 20, $end='...')}}
                                        </h6>
                                    </a>
                                </td>
                                <td class="text-center pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <div class="max-85 text-right">
                                        {{ $vendor->vendorproducts->count() }}
                                    </div>
                                </td>
                                <td class="text-center">
                                {{ $vendor->created_at->format('d M Y h:i:s') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="page-area">
                        <table>
                            <tfoot class="border-top">
                                {!! $vendors->links('pagination::bootstrap-4') !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($vendors)==0)
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

@push('script_2')
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