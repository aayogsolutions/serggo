@extends('Admin.layouts.app')

@section('title', translate('Subcategory Banner section'))

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
                {{translate('Subcategory Banner section')}}
            </span>
        </h1>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="form-group mb-0">
                                <label class="input-label" for="exampleFormControlInput1">
                                    {{translate('Category Name')}}
                                </label>
                                <input type="text" name="title" value="{{ translate($category->name) }}" class="form-control" maxlength="255" disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="btn--container justify-content-end">
                        <button type="button" class="btn btn--reset" onclick="location.reload()">{{translate('reset')}}</button>
                        @if($category->banner->count() != 8)
                            <button type="button" class="btn btn--primary" data-toggle="modal" data-target="#addmodel">{{translate('Add Content')}}</button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="addmodel" tabindex="-1" role="dialog" aria-labelledby="addmodel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('admin.banners.subcategory_banners.add.content',$category->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">{{translate($category->title)}}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="d-flex flex-column justify-content-center h-100">
                                            <h5 class="text-center mb-3 text--title text-capitalize">
                                                {{translate('banner')}} {{translate('image')}}
                                                <small class="text-danger">* ( {{translate('ratio')}} 1:2 )</small>
                                            </h5>
                                            <label class="upload--vertical">
                                                <input type="file" name="image" id="customFileEg1" class="" accept=".jpg, .png, .jpeg" hidden>
                                                <img class="" id="viewer" src="{{asset('assets/admin/img/upload-vertical.png')}}" alt="{{ translate('banner image') }}" />
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-group mb-0">
                                                    <label class="input-label" for="exampleFormControlSelect1">
                                                        {{translate('Sub')}} {{translate('category')}}
                                                        <span class="input-label-secondary">*</span>
                                                    </label>
                                                    <select name="subcategory" class="form-control show-item">
                                                        @foreach($subcategories as $subcategory)
                                                            <option value="{{$subcategory->id}}">{{translate($subcategory->name)}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Modal -->

        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="card--header justify-content-between max--sm-grow">
                <h5 class="card-title">{{translate('Item List')}} <span class="badge badge-soft-secondary">{{ $category->banner->count() }}</span></h5>

            </div>
        </div>

        <div class="table-responsive datatable-custom">
            <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{translate('#')}}</th>
                        <th class="border-0">{{translate('banner image')}}</th>
                        <th class="border-0">{{translate('SubCategory Item')}}</th>
                        <th class="border-0">{{translate('priority')}}</th>
                        <th class="text-center border-0">{{translate('action')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($category->banner as $key => $value)
                    <tr>
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            <div>
                                <img class="upload--vertical--preview" src="{{ asset($value->attechment) }}" alt="{{ translate('banner image') }}">
                            </div>
                        </td>
                        <td>
                            <div class="product-list-media">
                                <img class="upload--vertical--preview" src="{{ asset(json_decode($value->sub_category_detail)->image)}}" alt="{{ translate('banner image') }}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                <h6 class="name line--limit-2">
                                    {{\Illuminate\Support\Str::limit(json_decode($value->sub_category_detail)->name, 20, $end='...')}}
                                </h6>
                            </div>
                        </td>
                        <td>
                            <div class="max-85">
                                <select name="priority" class="custom-select"
                                    onchange="location.href='{{ route('admin.banners.subcategory_banners.priority', ['id' => $value['id'], 'priority' => '']) }}' + this.value">
                                    @for($i = 0; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $value->priority == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="action-btn btn--danger btn-outline-danger form-alert" href="javascript:"
                                    data-id="banner-{{$value['id']}}"
                                    data-message="{{ translate("Want to delete this") }}">
                                    <i class="tio-delete-outlined"></i>
                                </a>
                            </div>
                            <form action="{{route('admin.banners.subcategory_banners.delete',[$value['id']])}}" method="post" id="banner-{{$value['id']}}">
                                @csrf @method('delete')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if(count($category->banner) == 0)
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