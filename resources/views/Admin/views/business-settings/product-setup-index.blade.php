@extends('Admin.layouts.app')

@section('title', translate('Product Setup'))

@section('content')
<div class="content container-fluid">
    @include('Admin.views.business-settings.partial.business-settings-navmenu')
    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.product-setup-update')}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end">
                            @php($tax_status = Helpers_get_business_settings('product_gst_tax_status'))
                            <div class="col-md-4 col-sm-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{translate('Product GST/TAX Status (Included/Excluded)')}}</label>
                                    <select name="product_vat_tax_status" class="form-control">
                                        <option value="excluded" {{$tax_status =='excluded'?'selected':''}}>{{translate('excluded')}}</option>
                                        <option value="included" {{$tax_status =='included'?'selected':''}}>{{translate('included')}}</option>
                                    </select>
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

