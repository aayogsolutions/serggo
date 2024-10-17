@extends('Admin.layouts.app')

@section('title', translate('Cookies Setup'))

@section('content')
<div class="content container-fluid">
    @include('Admin.views.business-settings.partial.business-settings-navmenu')

    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <form action="{{route('admin.business-settings.store.cookies-setup-update')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap justify-content-between">
                                    <span class="">{{translate('Cookies Text')}}</span>
                                        <label class="change-cookies-status" data-route="{{route('admin.business-settings.store.cookies-status',[$cookies['status'] == 0 ? 1 : 0])}}">
                                            <input type="checkbox" class="toggle-switch-input" name="free_delivery_status" {{ $cookies['status'] == 0 ? 'checked' : '' }}>
                                            <span class="toggle-switch-label text">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                </div>
                                <div class="form-group pt-3">
                                    <textarea name="text" class="form-control" rows="6" placeholder="{{ translate('Cookies text') }}" required>{{$cookies['text']}}</textarea>
                                </div>
                                <div class="btn--container justify-content-end">
                                    <button type="submit" class="btn btn--primary call-demo">{{translate('save')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script>
        $('.change-cookies-status').on('click', function(){
            let route = $(this).data('route');

            $.get({
                url: route,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                    if (data.status == 1) {
                        toastr.success(data.message);
                    } else {
                        toastr.warning(data.message);
                    }

                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        })
    </script>
@endpush