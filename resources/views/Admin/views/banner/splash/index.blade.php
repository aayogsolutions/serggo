@extends('Admin.layouts.app')

@section('title', translate('Add new banner'))

@push('css')
    <style>
        .img-horizontal-150{
            height: 150px;
            aspect-ratio: 1/2;
            object-fit: cover;
            border-radius: 10px;
        }

        .table-responsive{
            max-height: 700px !important;
        }
    </style>
@endpush

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/banner.png')}}" class="w--20" alt="{{ translate('banner') }}">
            </span>
            <span>
                {{translate('splash banners setup')}}
            </span>
        </h1>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{route('admin.banners.splash.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('title')}}</label>
                                    <input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">
                                        {{translate('UI')}} {{translate('type')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="type" class="form-control show-item">
                                        <option value="user">{{translate('user')}}</option>
                                        <option value="vender">{{translate('vender')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex flex-column justify-content-center h-100">
                            <h5 class="text-center mb-3 text--title text-capitalize">
                                {{translate('banner')}} {{translate('image')}}
                                <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                            </h5>
                            <label class="upload--horizontal">
                                <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*,video/*" hidden>
                                <img id="viewer" src="{{asset('assets/admin/img/upload-horizontal.jpg')}}" alt="{{ translate('banner image') }}" />
                                <video id="viewervideo" src="" style="display: none;" autoplay loop></video>
                                <input type="hidden" name="width" id="videoWidth">
                                <input type="hidden" name="height" id="videoHeight">
                            </label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset" onclick="location.reload()">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="card--header justify-content-between max--sm-grow">
                <h5 class="card-title">{{translate('Banner List')}} <span class="badge badge-soft-secondary">{{ $banners->total() }}</span></h5>
                <form action="{{url()->current()}}" method="GET">
                    <div class="input-group">
                        <input type="search" name="search" class="form-control"
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
                        <th class="border-0">{{translate('#')}}</th>
                        <th class="border-0">{{translate('banner image')}}</th>
                        <th class="border-0">{{translate('title')}}</th>
                        <th class="border-0">{{translate('UI type')}}</th>
                        <th class="text-center border-0">{{translate('status')}}</th>
                        <th class="text-center border-0">{{translate('action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banners as $key => $banner)
                    <tr>
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            <div>
                                @if($banner->attechment_type == 'image')
                                    <img class="img-horizontal-150" src="{{ asset('Images/banners').'/'.$banner->attechment }}" alt="{{ translate('banner image') }}">
                                @else
                                    <video class="img-horizontal-150" src="{{ asset('Images/banners').'/'.$banner->attechment }}"onclick="this.paused?this.play():this.pause();" title="Click To play & Stop"></video>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="d-block font-size-sm text-body text-trim-25">
                                {{$banner['title']}}
                            </span>
                        </td>
                        <td>
                            {{ translate($banner->ui_type) }}
                        </td>
                        <td>
                            <label class="toggle-switch my-0">
                                <input type="checkbox"
                                    class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $banner->id }}"
                                    data-route="{{ route('admin.banners.splash.status', [$banner->id, $banner->status == 1 ? 0 : 1,$banner->ui_type]) }}"
                                    data-message="{{ $banner->status? translate('you_want_to_disable_this_banner'): translate('you_want_to_active_this_banner') }}"
                                    {{ $banner->status == 0 ? 'checked' : '' }}>
                                <span class="toggle-switch-label mx-auto text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="action-btn"
                                    href="{{route('admin.banners.splash.edit',[$banner['id']])}}">
                                    <i class="tio-edit"></i></a>
                                <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                    data-id="banner-{{$banner['id']}}"
                                    data-message="{{ translate("Want to delete this") }}">
                                    <i class="tio-delete-outlined"></i>
                                </a>
                            </div>
                            <form action="{{route('admin.banners.splash.delete',[$banner['id']])}}"
                                method="post" id="banner-{{$banner['id']}}">
                                @csrf @method('delete')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <table>
                <tfoot>
                    {!! $banners->links() !!}
                </tfoot>
            </table>

        </div>
        @if(count($banners) == 0)
        <div class="text-center p-4">
            <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
            <p class="mb-0">{{translate('No_data_to_show')}}</p>
        </div>
        @endif
    </div>
</div>

@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/banner.js') }}"></script>
@endpush