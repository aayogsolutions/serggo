@extends('Admin.layouts.app')

@section('title', translate('AMC Plan List'))

@section('content')
<div class="content container-fluid product-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/products.png')}}" class="w--24" alt="" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
            </span>
            <span>
                {{ translate('AMC Plan List') }}
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
                        <!-- <div class="hs-unfold mr-2">
                            <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                data-hs-unfold-options='{
                                            "target": "#usersExportDropdown",
                                            "type": "css-animation"
                                        }'>
                                <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                            </a>

                            <div id="usersExportDropdown"
                                class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                                <span class="dropdown-header">{{ translate('download') }}
                                    {{ translate('options') }}</span>
                                <a id="export-excel" class="dropdown-item" href="{{route('admin.product.bulk-export')}}">
                                    <img class="avatar avatar-xss avatar-4by3 mr-2"
                                        src="{{ asset('assets/admin') }}/svg/components/excel.svg"
                                        alt="Image Description">
                                    {{ translate('excel') }}
                                </a>
                            </div>
                        </div> -->
                        <!-- <div>
                            <a href="{{route('admin.product.limited-stock')}}" class="btn btn--primary-2 min-height-40">{{translate('limited stocks')}}</a>
                        </div> -->
                        <div>
                            <a href="{{route('admin.amc.plan.add-new')}}" class="btn btn-primary min-height-40 py-2">
                                <i class="tio-add"></i>
                                {{translate('add new Plan')}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive datatable-custom">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th>{{translate('plan_name')}}</th>
                                <th>{{translate('selling_price')}}</th>
                                <th class="text-center">{{translate('plan_duration')}}</th>
                                <th class="text-center">{{translate('status')}}</th>
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
                                    <a href="{{ route('admin.product.view',$product->id) }}" class="product-list-media">
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
                                    {{ translate($product->duration) }}
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" onclick="status_change_alert('{{ route('admin.amc.plan.status', [$product->id, $product->status == 1 ? 0 : 1]) }}', '{{ $product->status == 1? translate('you want to disable this plan'): translate('you want to active this plan') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $product->id }}" {{ $product->status == 1 ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <!-- Dropdown -->
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                            href="{{route('admin.amc.plan.update',[$product['id']])}}">
                                            <i class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('product-{{$product['id']}}','{{ translate("Want to delete this") }}')">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.amc.plan.delete',[$product['id']])}}"
                                        method="post" id="product-{{$product['id']}}">
                                        @csrf @method('delete')
                                    </form>
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

    function stack_adjust(url) {
        
        Swal.fire({
            title: '{{ translate("Are you sure?") }}',
            text: '{{ translate("You_want_to_change_stock") }}',
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#107980',
            cancelButtonText: '{{ translate("No") }}',
            confirmButtonText: '{{ translate("Yes") }}',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: url,
                    method: 'POST',
                    success: function() {
                        location.reload();
                    }
                });
            }
        })
        
    }
</script>
@endpush