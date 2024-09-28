@extends('Admin.layouts.app')

@section('title', translate('Add new brands'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/category.png')}}" class="w--24" alt="{{ translate('brand') }}">
            </span>
            <span>
                {{translate('brands_setup')}}
            </span>
        </h1>
    </div>

    <div class="row g-2">
        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-body pt-sm-0 pb-sm-4">
                    <form action="{{route('admin.brands.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end g-4">
                            <div class="lang_form col-sm-6" id="-form">
                                <label class="form-label" for="exampleFormControlInput1">
                                    {{translate('brand')}} {{ translate('name') }}
                                </label>
                                <input type="text" name="name" class="form-control" maxlength="255" placeholder="{{ translate('New brand') }}" required>
                            </div>
                            
                            <div class="col-sm-6">
                                <div>
                                    <div class="text-center mb-3">
                                        <img id="viewer" class="img--105" src="{{ asset('assets/admin/img/160x160/1.png') }}" alt="{{ translate('image') }}" />
                                    </div>
                                </div>
                                <label class="form-label text-capitalize">{{ translate('brand image') }}</label><small class="text-danger">* ( {{ translate('ratio') }}3:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required oninvalid="document.getElementById('en-link').click()">
                                    <label class="custom-file-label" for="customFileEg1">{{ translate('choose') }}
                                        {{ translate('file') }}</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="btn--container justify-content-end">
                                    <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                                    <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="card--header">
                        <h5 class="card-title">{{translate('brand Table')}} <span class="badge badge-soft-secondary">{{ $brands->total() }}</span> </h5>
                        <form action="{{url()->current()}}" method="GET">
                            <div class="input-group">
                                <input id="datatableSearch_" type="search" name="search" maxlength="255"
                                    class="form-control pl-5"
                                    placeholder="{{translate('Search_by_Name')}}" aria-label="Search"
                                    value="{{$search}}" required autocomplete="off">
                                <i class="tio-search tio-input-search"></i>
                                <div class="input-group-append">
                                    <button type="submit" class="input-group-text">
                                        {{translate('search')}}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Table -->
                <div class="table-responsive datatable-custom">
                    <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center">{{translate('#')}}</th>
                                <th>{{translate('brand_image')}}</th>
                                <th>{{translate('name')}}</th>
                                <th>{{translate('status')}}</th>
                                <th>{{translate('priority')}}</th>
                                <th class="text-center">{{translate('action')}}</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($brands as $key => $brand)
                            <tr>
                                <td class="text-center">{{$brands->firstItem()+$key}}</td>
                                <td>
                                    <img src="{{ asset($brand->Image)}}" class="img--50 ml-3"
                                        alt="{{ translate('brand') }}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body text-trim-50">
                                        {{translate($brand['name'])}}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch">
                                        <input type="checkbox"
                                            class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $brand->id }}"
                                            data-route="{{ route('admin.brands.status', [$brand->id, $brand->status == 1 ? 0 : 1]) }}"
                                            data-message="{{ $brand->status == 0 ? translate('you_want_to_disable_this_brand'): translate('you_want_to_active_this_brand') }}"
                                            {{ $brand->status == 0 ? 'checked' : '' }}>
                                        <span class="toggle-switch-label text">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="max-85">
                                        <select name="priority" class="custom-select"
                                            onchange="location.href='{{ route('admin.brands.priority', ['id' => $brand['id'], 'priority' => '']) }}' + this.value">
                                            @for($i = 1; $i <= 10; $i++)
                                                <option value="{{ $i }}" {{ $brand->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn--container justify-content-center">
                                        <a class="action-btn"
                                            href="{{route('admin.brands.edit',[$brand['id']])}}">
                                            <i class="tio-edit"></i></a>
                                        <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                            data-id="brand-{{$brand['id']}}"
                                            data-message="{{ translate("Want to delete this") }}?">
                                            <i class="tio-delete-outlined"></i>
                                        </a>
                                    </div>
                                    <form action="{{route('admin.brands.delete',[$brand['id']])}}"
                                        method="post" id="brand-{{$brand['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>


                    @if(count($brands) == 0)
                    <div class="text-center p-4">
                        <img class="w-120px mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                        <p class="mb-0">{{translate('No_data_to_show')}}</p>
                    </div>
                    @endif

                    <table>
                        <tfoot>
                            {!! $brands->links() !!}
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/category.js') }}"></script>
@endpush