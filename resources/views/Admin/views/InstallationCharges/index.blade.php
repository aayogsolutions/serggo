@extends('Admin.layouts.app')

@section('title', translate('Add new installation'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/attribute.png')}}" class="w--24" alt="{{ translate('attribute') }}">
                </span>
                <span>
                    {{ translate('installation Setup') }}
                </span>
            </h1>
        </div>
        <div class="card">
            <div class="card-header border-0">
                <div class="card--header">
                    <h5 class="card-title">{{translate('Installations Table')}} <span class="badge badge-soft-secondary">{{ $installations->total() }}</span> </h5>
                    <button class="btn btn--primary ml-lg-4">
                        <a href="{{ route('admin.installation.add-new') }}" style="text-decoration: none;color:#ffffff;">
                            {{translate('Reset')}}
                        </a>
                    </button>
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search"
                                class="form-control"
                                placeholder="{{translate('Search')}}" aria-label="Search"
                                value="{{$search}}" required autocomplete="off">
                            <div class="input-group-append">
                                <button type="submit" class="input-group-text">
                                    <i class="tio-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <button class="btn btn--primary ml-lg-4" data-toggle="modal" data-target="#installations-modal"><i class="tio-add"></i> {{translate('add_installation')}}</button>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('#')}}</th>
                        <th>{{translate('name')}}</th>
                        <th>{{translate('description')}}</th>
                        <th>{{translate('charges')}}</th>
                        <th class="text-center">{{translate('status')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($installations as $key=>$installation)
                        <tr>
                            <td>{{$installations->firstItem()+$key}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-70">
                                    {{$installation['installation_name']}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-70">
                                    {{$installation['installation_description']}}
                                </span>
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-70">
                                    {{Helpers_set_symbol($installation['installation_charges'])}}
                                </span>
                            </td>
                            <td class="pt-1 pb-3  {{$key == 0 ? 'pt-4' : '' }}">
                                    <label class="toggle-switch my-0">
                                        <input type="checkbox"
                                            onclick="status_change_alert('{{ route('admin.installation.status', [$installation->id, $installation->status == 1 ? 0 : 1]) }}', '{{ $installation->status? translate('you want to disable this installation'): translate('you want to active this installation') }}', event)"
                                            class="toggle-switch-input" id="stocksCheckbox{{ $installation->id }}"
                                            {{ $installation->status == 0 ? 'checked' : '' }}>
                                        <span class="toggle-switch-label mx-auto text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn"
                                        href="{{route('admin.installation.edit',[$installation['id']])}}">
                                    <i class="tio-edit"></i></a>
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="installation-{{$installation['id']}}"
                                        data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.installation.delete',[$installation['id']])}}"
                                        method="post" id="installation-{{$installation['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <table>
                    <tfoot>
                    {!! $installations->links('pagination::bootstrap-4') !!}
                    </tfoot>
                </table>

                @if(count($installations) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="installations-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.installation.store')}}" method="post">
                    <div class="modal-body pt-3">
                        @csrf
                        @php($data = helpers_get_business_settings('language'))
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                                    <input type="text" name="installation_name" class="form-control" placeholder="{{translate('New installation')}}" maxlength="255">
                                </div>
                                <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('Description')}}</label>
                                    <textarea name="description" class="form-control"></textarea>
                                </div>
                                <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('Charges')}}</label>
                                    <input type="number" name="charges" class="form-control" placeholder="{{translate('installation charges')}}">
                                </div>
                            </div>
                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset" data-dismiss="modal">{{translate('cancel')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
