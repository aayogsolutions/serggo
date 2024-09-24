@extends('Admin.layouts.app')

@section('title', translate('Update category'))

@section('content')
<div class="content container-fluid">
    <div class="page-header">
        <h1 class="page-header-title">
            <span class="page-header-icon">
                <img src="{{asset('assets/admin/img/category.png')}}" class="w--24" alt="{{ translate('category') }}">
            </span>
            <span>
                @if($category->parent_id == 0)
                {{ translate('category Update') }}
                @else
                {{ translate('Sub Category Update') }}
                @endif
            </span>
        </h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{route('admin.category.update',[$category['id']])}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row align-items-end g-4">
                    <div class="col-sm-6 lang_form" id="-form">
                        <label class="input-label"
                            for="exampleFormControlInput1">{{translate('name')}}
                            </label>
                        <input type="text" name="name" value="{{$category['name']}}"
                            class="form-control" oninvalid="document.getElementById('en-link').click()"
                            placeholder="{{ translate('New Category') }}" required>
                    </div>
                    <input name="position" value="0" hidden>
                    <div class="col-sm-6">
                        <div class="text-center">
                            <img class="img--105" id="viewer"
                                src="{{ asset('Images/category').'/'.$category->image}}"
                                alt="{{ translate('category') }}" onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'"/>
                        </div>
                        <label>{{translate('image')}}</label><small class="text-danger">* ( {{translate('ratio')}} 3:1 )</small>
                        <div class="custom-file">
                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="customFileEg1">{{translate('choose')}} {{translate('file')}}</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary">{{translate('update')}}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('script_2')
<script src="{{ asset('assets/admin/js/category.js') }}"></script>
@endpush