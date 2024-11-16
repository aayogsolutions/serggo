@extends('Admin.layouts.app')

@section('title', translate('Service List'))

@section('content')

<div class="content container-fluid product-list-page">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/products.png')}}" class="w--24" alt="" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
            </span>
            <span>
                {{ translate('Service List') }}
                <span class="badge badge-soft-secondary">{{ $service->total() }}</span>
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
                        <div class="hs-unfold mr-2">
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
                        </div>
                        
                        <div>
                            <a href="{{route('admin.service.add-new')}}" class="btn btn-primary min-height-40 py-2"><i
                                    class="tio-add"></i>
                                {{translate('add new Service')}}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-responsive datatable-custom">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th>{{translate('#')}}</th>
                                <th>{{translate('Service_name')}}</th>
                                <th class="text-center">{{translate('Time Duration')}}</th>
                                <th class="text-center">{{translate('Status')}}</th>
                                <th class="text-center">{{translate('Action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="set-rows">
                       
                            @foreach($service as $key=>$services)
                            <tr>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    {{$key+1}}
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <a href="#" class="product-list-media">
                                        @if (!empty(json_decode($services['image'],true)))
                                        <img src="{{ asset(json_decode($services->image)[0])}}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                        @else
                                        <img src="{{asset('assets/admin/img/400x400/img2.jpg')}}">
                                        @endif
                                        <h6 class="name line--limit-2">
                                            {{\Illuminate\Support\Str::limit($services['name'], 20, $end='...')}}
                                        </h6>
                                    </a>
                                </td>
                                <td class="text-center">
                                    {{ translate($services->time_duration) }}
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox"
                                            onclick="status_change_alert('{{ route('admin.service.status', [$services->id, $services->status == 1 ? 0 : 1]) }}', '{{ $services->status? translate('you want to disable this service'): translate('you want to active this service') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $services->id }}"
                                            {{ $services->status == 0 ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <!-- Dropdown -->
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                            href="{{route('admin.service.edit',[$services['id']])}}">
                                            <i class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger" href="javascript:"
                                            onclick="form_alert('service-{{$services['id']}}','{{ translate("Want to delete this") }}')">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.service.delete',[$services['id']])}}"
                                        method="post" id="service-{{$services['id']}}">
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
                                {!! $service->links('pagination::bootstrap-4') !!}
                            </tfoot>
                        </table>
                    </div>
                    @if(count($service)==0)
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
            url: "{{route('admin.service.daily-needs')}}",
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