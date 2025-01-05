@extends('Admin.layouts.app')

@section('title', translate('About us'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            @include('Admin.views.pages_&_media.partial.page-setup-menu')
        </div>
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.business-settings.page-setup.about-us')}}" method="post" id="tnc-form">
                    @csrf
                    <label for="">
                        {{translate('user_privacy_policy')}}
                    </label>
                    <div class="form-group">
                        <textarea class="ckeditor form-control" name="about_us">{!! $data->value !!}</textarea>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.business-settings.page-setup.about-us.partner')}}" method="post" id="tnc-form">
                    @csrf
                    <label for="">
                        {{translate('partner_privacy_policy')}}
                    </label>
                    <div class="form-group">
                        <textarea class="ckeditor form-control" name="about_us">{!! $partner_data->value !!}</textarea>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.business-settings.page-setup.about-us.vendor')}}" method="post" id="tnc-form">
                    @csrf
                    <label for="">
                        {{translate('vendor_privacy_policy')}}
                    </label>
                    <div class="form-group">
                        <textarea class="ckeditor form-control" name="about_us">{!! $vendor_data->value !!}</textarea>
                    </div>
                    <div class="btn--container justify-content-end">
                        <button type="reset" class="btn btn--reset" id="reset">{{translate('reset')}}</button>
                        <button type="submit" class="btn btn--primary">{{translate('submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });

        $('#reset').click(function() {
            location.reload();
        });
    </script>
@endpush
