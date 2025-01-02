@extends('Admin.layouts.app')

@section('title', translate('Order Setup'))

@section('content')
<div class="content container-fluid">
    @include('Admin.views.business-settings.partial.business-settings-navmenu')

    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.commission-setup-update')}}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 pt-5">
                                <div class="form-group">
                                    <label class="toggle-switch h--45px toggle-switch-sm d-flex justify-content-between border rounded px-3 py-0 form-control">
                                        <span class="pr-1 d-flex align-items-center switch--label">
                                            <span class="line--limit-1">
                                                <strong>
                                                    {{translate('Service Man Commission')}}
                                                </strong>
                                            </span>
                                            <span class="form-label-secondary text-danger d-flex ml-1" data-toggle="tooltip" data-placement="right" data-original-title="{{translate('If this field is Stands for Service Man Commission apply')}}">
                                                <img src="{{asset('assets/admin/img/info-circle.svg')}}" alt="info">
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group mb-0">
                                    <label class="input-label text-capitalize">
                                        {{translate('Service Man Commission in (%)')}}
                                    </label>
                                    <input type="number" value="{{$serviceman_commission}}"  step="0.1" name="serviceman_commission" class="form-control" placeholder="">
                                </div>
                            </div>
                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn--primary call-demo">{{translate('save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script>
        $('#order-status').on('click', function(){
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