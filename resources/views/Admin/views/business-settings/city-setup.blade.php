@extends('Admin.layouts.app')

@section('title', translate('Add new Time Slot'))

@section('content')
    <div class="content container-fluid">
        @include('Admin.views.business-settings.partial.business-settings-navmenu')
        <div class="card mb-2">
            <div class="card-header">
                <h5 class="card-title">
                    <span class="card-header-icon">
                        <i class="tio-clock"></i>
                    </span> <span>{{translate('User_cities')}}</span>
                </h5>
            </div>
            <div class="card-body">
                <form action="{{route('admin.business-settings.store.city-setup-update')}}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label"> {{translate('City')}} {{translate('name')}} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="{{ translate('Indore') }}" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="form-label"> {{translate('city')}} {{translate('range')}} <span class="text-danger">*</span></label>
                                <input type="number" name="km" class="form-control" placeholder="{{ translate('Ex: 50') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>

        </div>
        <div class="card">
            <div class="card-header border-0 py-1">
                <h5 class="card-header-title py-3 d-flex align-items-center">
                    <span>{{translate('Cities List')}}</span> 
                    <span class="ml-1 badge-pill py-1 px-2 badge badge-soft-secondary">{{count($cities)}}</span>
                </h5>
            </div>
            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="columnSearchDatatable" class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                        data-hs-datatables-options='{
                            "order": [],
                            "orderCellsTop": true
                        }'>
                    <thead class="thead-light">
                    <tr>
                        <th class="text-center">{{translate('#')}}</th>
                        <th class="text-center">{{translate('City')}} {{translate('name')}} </th>
                        <th class="text-center">{{translate('city')}} {{translate('range')}}  </th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>

                    <tbody>

                        @foreach($cities as $key=>$City)
                            <tr>
                                <td class="text-center">{{$key+1}}</td>
                                <td class="text-center">
                                    <div>{{ $City->name }}</div>
                                </td>
                                <td class="text-center">
                                    <div>{{ $City->km }}</div>
                                </td>
                                <td>
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox" onclick="status_change_alert('{{ route('admin.business-settings.store.city.status', $City->id) }}', '{{ $City->status == 0 ? translate('you_want_to_disable_this_city'): translate('you_want_to_active_this_city') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $City->id }}"
                                            {{ $City->status == 0 ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <!-- Dropdown -->
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn" href="{{route('admin.business-settings.store.city.edit',[$City['id']])}}">
                                            <i class="tio-edit"></i>
                                        </a>
                                        <a class="action-btn btn--danger btn-outline-danger" href="javascript:" onclick="form_alert('timeSlot-{{$City['id']}}','{{ translate("Want to delete this") }}')">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.business-settings.store.city.delete',[$City['id']])}}" method="post" id="timeSlot-{{$City['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                    <!-- End Dropdown -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(count($cities) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
                @endif
            </div>
        </div>
        <!-- End Table -->
    </div>
    
@endsection

@push('script_2')
<script>
        function status_change_alert(url, message, e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: message,
                type: 'warning',
                showCancelButton: true,
                cancelButtonColor: 'default',
                confirmButtonColor: '#107980',
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    location.href = url;
                }
            })
        }
</script>
@endpush
