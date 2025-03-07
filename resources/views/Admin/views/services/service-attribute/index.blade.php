@extends('Admin.layouts.app')

@section('title', translate('Add new service attribute'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{asset('assets/admin/img/attribute.png')}}" class="w--24" alt="{{ translate('attribute') }}">
                </span>
                <span>
                    {{ translate('Service Attribute Setup') }}
                </span>
            </h1>
        </div>
        <div class="card">
            <div class="card-header border-0">
                <div class="card--header">
                    <h5 class="card-title">{{translate('Service Attribute Table')}} <span class="badge badge-soft-secondary">{{ $attributes->total() }}</span> </h5>
                    <button class="btn btn--primary ml-lg-4">
                        <a href="{{ route('admin.service.attribute.add-new') }}" style="text-decoration: none;color:#ffffff;">
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
                    <button class="btn btn--primary ml-lg-4" data-toggle="modal" data-target="#attribute-modal"><i class="tio-add"></i> {{translate('add_attribute')}}</button>
                </div>
            </div>
            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                    <tr>
                        <th>{{translate('#')}}</th>
                        <th>{{translate('name')}}</th>
                        <th class="text-center">{{translate('action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($attributes as $key=>$attribute)
                        <tr>
                            <td>{{$attributes->firstItem()+$key}}</td>
                            <td>
                                <span class="d-block font-size-sm text-body text-trim-70">
                                    {{$attribute['name']}}
                                </span>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn"
                                        href="{{route('admin.service.attribute.edit',[$attribute['id']])}}">
                                    <i class="tio-edit"></i></a>
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="attribute-{{$attribute['id']}}"
                                        data-message="{{ translate("Want to delete this") }}">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.service.attribute.delete',[$attribute['id']])}}"
                                        method="post" id="attribute-{{$attribute['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

                <table>
                    <tfoot>
                    {!! $attributes->links('pagination::bootstrap-4') !!}
                    </tfoot>
                </table>

                @if(count($attributes) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" id="attribute-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('admin.service.attribute.store')}}" method="post">
                    <div class="modal-body pt-3">
                        @csrf
                        @php($data = helpers_get_business_settings('language'))
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group lang_form" id="-form">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('name')}}</label>
                                    <input type="text" name="name" class="form-control" placeholder="{{translate('New attribute')}}" maxlength="255">
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
