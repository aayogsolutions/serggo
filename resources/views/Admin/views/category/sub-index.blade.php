@extends('Admin.layouts.app')

@section('title', translate('Add new sub category'))

@push('css_or_js')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/category.png')}}" class="w--24" alt="{{ translate('category') }}">
            </span>
            <span>
                {{translate('sub_category_setup')}}
            </span>
        </h1>
    </div>
    <div class="row gx-2 gx-lg-3">
        <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.category.store')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <br>
                                <div class="lang_form" id="-form">
                                    <label class="form-label" for="exampleFormControlInput1">{{translate('sub_category')}} {{translate('name')}}</label>
                                    <input type="text" name="name" class="form-control" placeholder="{{ translate('New Sub Category') }}" required>
                                </div>

                                <input name="position" value="1" hidden>
                                <br>
                                <div class="">
                                    <div class="form-group">
                                        <label class="form-label"
                                            for="exampleFormControlSelect1">{{translate('main')}} {{translate('category')}}
                                            <span class="input-label-secondary">*</span></label>
                                        <select id="exampleFormControlSelect1" name="parent_id" class="form-control" required>
                                            @foreach(\App\Models\Category::where(['position'=>0])->get() as $category)
                                            <option value="{{$category['id']}}">{{$category['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <div class="text-center mb-3">
                                        <img id="viewer" class="img--105" src="{{ asset('assets/admin/img/160x160/1.png') }}" alt="{{ translate('image') }}" />
                                    </div>
                                </div>
                                <label class="form-label text-capitalize">{{ translate('Sub category image') }}</label>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input" accept="image/*" required>
                                    <label class="custom-file-label" for="customFileEg1">
                                        {{ translate('choose') }}{{ translate('file') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="btn--container justify-content-end">
                                <a href="" class="btn btn--reset min-w-120px">{{translate('reset')}}</a>
                                <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                            </div>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
        <div class="card">
            <div class="card-header border-0">
                <div class="card--header">
                    <h5 class="card-title">{{translate('Sub Category Table')}} <span class="badge badge-soft-secondary">{{ $categories->total() }}</span> </h5>
                    <form action="{{url()->current()}}" method="GET">
                        <div class="input-group">
                            <input id="datatableSearch_" type="search" name="search"
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
            <div class="table-responsive datatable-custom">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">{{translate('#')}}</th>
                            <th class="text-center">{{translate('Image')}}</th>
                            <th>{{translate('main')}} {{translate('category')}}</th>
                            <th>{{translate('sub_category')}}</th>
                            <th>{{translate('status')}}</th>
                            <th>{{translate('Installable')}}</th>
                            <th class="text-center">{{translate('action')}}</th>
                        </tr>

                    </thead>

                    <tbody id="set-rows">
                        @foreach($categories as $key=>$category)
                        <tr>
                            <td class="text-center">{{$categories->firstItem()+$key}}</td>
                            <td>
                                <img src="{{ asset($category->image)}}" class="img--50 ml-3"
                                    alt="{{ translate('category') }}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                            </td>
                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$category->parent['name']}}
                                </span>
                            </td>

                            <td>
                                <span class="d-block font-size-sm text-body">
                                    {{$category['name']}}
                                </span>
                            </td>

                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $category->id }}"
                                        data-route="{{ route('admin.category.status', [$category->id, $category->status == 1 ? 0 : 1]) }}"
                                        data-message="{{ $category->status == 0 ? translate('you_want_to_disable_this_category'): translate('you_want_to_active_this_category') }}"
                                        {{ $category->status == 0 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>
                            </td>

                            <td>
                                <label class="toggle-switch">
                                    <input type="checkbox" class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $category->id }}"
                                        data-route="{{ route('admin.category.intallable', [$category->id, $category->is_installable == 1 ? 0 : 1]) }}"
                                        data-message="{{ $category->status == 0 ? translate('you_want_to_disable_installation'): translate('you_want_to_active_installation') }}"
                                        {{ $category->is_installable == 0 ? 'checked' : '' }}>
                                    <span class="toggle-switch-label text">
                                        <span class="toggle-switch-indicator"></span>
                                    </span>
                                </label>

                            </td>

                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="action-btn"
                                        href="{{route('admin.category.edit',[$category['id']])}}">
                                        <i class="tio-edit"></i></a>
                                    <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                        data-id="category-{{$category['id']}}"
                                        data-message="{{ translate("Want to delete this") }}?">
                                        <i class="tio-delete-outlined"></i>
                                    </a>
                                </div>
                                <form action="{{route('admin.category.delete',[$category['id']])}}"
                                    method="post" id="category-{{$category['id']}}">
                                    @csrf @method('delete')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                @if(count($categories) == 0)
                <div class="text-center p-4">
                    <img class="w-120px mb-3" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
                    <p class="mb-0">{{translate('No_data_to_show')}}</p>
                </div>
                @endif

                <div class="page-area">
                    <table>
                        <tfoot>
                            {!! $categories->links('pagination::bootstrap-4') !!}
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