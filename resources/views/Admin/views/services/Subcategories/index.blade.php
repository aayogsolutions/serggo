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

    .category_img{
        display: inline;
        height: 50px;
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
                {{translate('Sub Categories Banner setup')}}
            </span>
        </h1>
    </div>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">               
                <div class="col-4">
                    <div class="alert alert-warning">
                        {{ translate('Add Minimum 1 Subcategory banner in every category') }}
                    </div>
                </div>                    
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header border-0">
            <div class="card--header justify-content-between max--sm-grow">
                <h5 class="card-title">{{translate('Banner List')}} <span class="badge badge-soft-secondary">{{ $categories->total() }}</span></h5>
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
                        <th class="border-0">{{translate('Category Name')}}</th>
                        <th class="border-0">{{translate('Number Of Banners')}}</th>                       
                        <th class="text-center border-0">{{translate('action')}}</th>
                    </tr>
                </thead>
                <tbody>
               
                    @foreach($categories as $key => $category)
                    <tr>
                        <td>
                            {{$key+1}}
                        </td>
                        <td>
                            <div class="product-list-media">
                                <img src="{{ asset($category->image)}}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                                <h6 class="name line--limit-2">
                                    {{\Illuminate\Support\Str::limit($category['name'], 20, $end='...')}}
                                </h6>
                            </div>
                        </td>
                        <td>
                            @if($category->banner == null)
                                {{0}}
                                <svg enable-background="new 0 0 64 64" height="35px" version="1.1" viewBox="0 0 64 64" width="64px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                    <g id="Layer_1">
                                        <g>
                                            <circle cx="32" cy="32" fill="#C75C5C" r="32"/>
                                        </g>
                                        <g opacity="0.2">
                                            <path d="M16.954,50c-4.4,0-6.2-3.118-4-6.928L28,17.012c2.2-3.811,5.8-3.811,8,0l15.046,26.06    c2.2,3.811,0.4,6.928-4,6.928H16.954z" fill="#231F20"/>
                                        </g>
                                        <g>
                                            <path d="M16.954,48c-4.4,0-6.2-3.118-4-6.928L28,15.012c2.2-3.811,5.8-3.811,8,0l15.046,26.06    c2.2,3.811,0.4,6.928-4,6.928H16.954z" fill="#F5CF87"/>
                                        </g>
                                        <g>
                                            <path d="M34,32c0,1.105-0.895,2-2,2l0,0c-1.105,0-2-0.895-2-2v-8c0-1.105,0.895-2,2-2l0,0c1.105,0,2,0.895,2,2V32z    " fill="#4F5D73"/>
                                        </g>
                                        <g>
                                            <path d="M34,40c0,1.105-0.895,2-2,2l0,0c-1.105,0-2-0.895-2-2l0,0c0-1.105,0.895-2,2-2l0,0    C33.105,38,34,38.895,34,40L34,40z" fill="#4F5D73"/>
                                        </g>
                                    </g>
                                    <g id="Layer_2"/>
                                </svg>
                            @else
                                <div>
                                    <img class="upload--vertical--preview" src="{{ asset($category->banner->attechment) }}" alt="{{ translate('banner image') }}">
                                </div> 
                            @endif
                        </td>
                        <td>
                            <div class="btn--container justify-content-center">
                                <a class="action-btn" href="javascript:" data-toggle="modal" data-target="#addmodel-{{$category->id}}">
                                    <i class="tio-edit"></i>
                                </a>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="addmodel-{{$category->id}}" tabindex="-1" role="dialog" aria-labelledby="addmodel-{{$category->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.service.subcategory_banners.add.content',$category->id) }}" method="post" enctype="multipart/form-data">
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
                                                                <img class="" id="viewer" src="{{ $category->banner == null ? asset('assets/admin/img/upload-vertical.png') : asset($category->banner->attechment)}}" alt="{{ translate('banner image') }}" />
                                                            </label>
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
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <table>
                <tfoot>
                    {!! $categories->links('pagination::bootstrap-4') !!}
                </tfoot>
            </table>

        </div>
        @if(count($categories) == 0)
        <div class="text-center p-4">
            <img class="w-120px mb-3" src="{{asset('/assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('image') }}">
            <p class="mb-0">{{translate('No_data_to_show')}}</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal -->

@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/banner.js') }}"></script>
@endpush