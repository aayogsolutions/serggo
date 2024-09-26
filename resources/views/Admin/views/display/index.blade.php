@extends('Admin.layouts.app')

@section('title', translate('Add new banner'))

@push('css')
<style>
    .upload--vertical--preview {
        height: 50px;
    }

    .table-responsive {
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
                {{translate('home display setup')}}
            </span>
        </h1>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                @foreach($data['box_section'] as $box_section)
                    @if($box_section->childes->count() < 6)
                        <div class="col-4">
                            <div class="alert alert-warning">
                                {{ translate('Add Minimum 6 Product/Category in box Section') }}
                            </div>
                        </div>
                        @break
                    @endif
                @endforeach
                @foreach($data['cart'] as $cart)
                    @if($cart->childes->count() < 6)
                        <div class="col-4">
                            <div class="alert alert-warning">
                                {{ translate('Add Minimum 6 Product in cart Section') }}
                            </div>
                        </div>
                        @break
                    @endif
                @endforeach
                @foreach($data['slider'] as $slider)
                    @if($slider->childes->count() < 6)
                        <div class="col-4">
                            <div class="alert alert-warning">
                                {{ translate('Add Minimum 6 Product in slider Section') }}
                            </div>
                        </div>
                        @break
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{route('admin.display.store')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlInput1">
                                        {{translate('section title')}}
                                    </label>
                                    <input type="text" name="title" value="{{old('title')}}" class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">
                                        {{translate('UI')}} {{translate('type')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="type" class="form-control">
                                        <option value="user_product">{{translate('user_product')}}</option>
                                        <option value="user_service">{{translate('user_service')}}</option>
                                        <option value="vender_service">{{translate('vender_service')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-0">
                                    <label class="input-label" for="exampleFormControlSelect1">{{translate('section')}} {{translate('type')}}
                                        <span class="input-label-secondary">*</span>
                                    </label>
                                    <select name="section_type" class="form-control show-item">
                                        <option value="slider">{{translate('slider')}}</option>
                                        <option value="cart">{{translate('cart')}}</option>
                                        <option value="box_section">{{translate('box_section')}}</option>
                                    </select>
                                </div>
                            </div>
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
                        <th class="border-0">{{translate('section title')}}</th>
                        <th class="border-0">{{translate('section type')}}</th>
                        <th class="border-0">{{translate('UI type')}}</th>
                        <th class="border-0">{{translate('childes')}}</th>
                        <th class="text-center border-0">{{translate('status')}}</th>
                        <th class="text-center border-0">{{translate('priority')}}</th>
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
                            <span class="d-block font-size-sm text-body text-trim-25">
                                {{$banner['title']}}
                            </span>
                        </td>
                        <td>
                            {{ translate($banner->section_type) }}
                        </td>
                        <td>
                            {{ translate($banner->ui_type) }}
                        </td>
                        <td>
                            {{ translate($banner->childes->count()) }}
                        </td>
                        <td>
                            <label class="toggle-switch my-0">
                                <input type="checkbox"
                                    class="toggle-switch-input status-change-alert" id="stocksCheckbox{{ $banner->id }}"
                                    data-route="{{ route('admin.display.status', [$banner->id, $banner->status == 1 ? 0 : 1,$banner->ui_type,$banner->ui_type]) }}"
                                    data-message="{{ $banner->status? translate('you_want_to_disable_this_banner'): translate('you_want_to_active_this_banner') }}"
                                    {{ $banner->status == 0 ? 'checked' : '' }}>
                                <span class="toggle-switch-label mx-auto text">
                                    <span class="toggle-switch-indicator"></span>
                                </span>
                            </label>
                        </td>
                        <td>
                            <div class="max-85">
                                <select name="priority" class="custom-select"
                                    onchange="location.href='{{ route('admin.display.section.priority', ['id' => $banner['id'], 'priority' => '']) }}' + this.value">
                                    @for($i = 1; $i <= 6; $i++)
                                        <option value="{{ $i }}" {{ $banner->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="action-btn" href="{{ route('admin.display.edit',$banner['id']) }}">
                                    <i class="tio-invisible"></i>
                                </a>
                                <a class="action-btn" href="javascript:void(0)" onclick="Edit_section('{{ $banner->id }}')">
                                    <i class="tio-edit"></i>
                                </a>
                                <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                    data-id="banner-{{$banner['id']}}"
                                    data-message="{{ translate("Want to delete this") }}">
                                    <i class="tio-delete-outlined"></i>
                                </a>
                            </div>
                            <form action="{{route('admin.display.delete',[$banner['id']])}}" method="post" id="banner-{{$banner['id']}}">
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

<!-- Modal -->
<div class="modal fade" id="updatemodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{route('admin.display.update.section')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">{{translate('Edit Section')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlInput1">
                                            {{translate('section title')}}
                                        </label>
                                        <input type="text" name="title" id="title" value="{{old('title')}}" class="form-control" placeholder="{{ translate('New banner') }}" maxlength="255" required>
                                        <input type="hidden" name="id" id="id" value="{{old('title')}}">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">
                                            {{translate('UI')}} {{translate('type')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="type" id="type" class="form-control">
                                            <option value="user_product">{{translate('user_product')}}</option>
                                            <option value="user_service">{{translate('user_service')}}</option>
                                            <option value="vender_service">{{translate('vender_service')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group mb-0">
                                        <label class="input-label" for="exampleFormControlSelect1">{{translate('section')}} {{translate('type')}}
                                            <span class="input-label-secondary">*</span>
                                        </label>
                                        <select name="section_type" id="section_type" class="form-control show-item">
                                            <option value="slider">{{translate('slider')}}</option>
                                            <option value="cart">{{translate('cart')}}</option>
                                            <option value="box_section">{{translate('box_section')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/banner.js') }}"></script>
<script>
    function Edit_section(id) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: "POST",
            url: "{{route('admin.display.detail.section')}}",
            data: {
                'id': id
            },
            success: function(data) {
                if (data.success) {
                    $('#id').val(data.data.id);
                    $('#title').val(data.data.title);
                    $('#type').val(data.data.ui_type);
                    $('#section_type').val(data.data.section_type);
                    $('#updatemodel').modal('show');
                }else{
                    alert(data.data);
                }
            }
        });
    }
</script>
@endpush