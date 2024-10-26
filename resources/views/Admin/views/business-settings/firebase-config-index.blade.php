@extends('Admin.layouts.app')

@section('title', translate('firebase configuration'))

@section('content')
<div class="content container-fluid">
    @include('Admin.views.business-settings.partial.business-settings-navmenu')
    <div class="tab-content">
        <div class="tab-pane fade show active" id="business-setting">
            <div class="card">

                <div class="card-body">
                    <form action="{{route('admin.business-settings.store.firebase_message_config')}}" method="post"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">{{translate('API Key')}}</label><br>
                            <input type="text"
                                placeholder="{{translate('Ex : AIzaSyDuBlqmsh9xw17osLOuEn7iqHtDlpkulcM')}}"
                                class="form-control" name="apiKey" value="{{$firebasemessageconfig['apiKey']}}" required
                                autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{translate('Auth Domain')}}</label><br>
                            <input type="text" class="form-control" name="authDomain"
                                value="{{$firebasemessageconfig['authDomain']}}" required autocomplete="off"
                                placeholder="{{translate('Ex : grofresh-3986f.firebaseapp.com')}}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{translate('Project ID')}}</label><br>
                            <input type="text" class="form-control" name="projectId"
                                value="{{$firebasemessageconfig['projectId']}}" required autocomplete="off"
                                placeholder="{{translate('Ex : grofresh-3986f')}}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{translate('Storage Bucket')}}</label><br>
                            <input type="text" class="form-control" name="storageBucket"
                                value="{{$firebasemessageconfig['storageBucket']}}" required autocomplete="off"
                                placeholder="{{translate('Ex : grofresh-3986f.appspot.com')}}">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{translate('Messaging Sender ID')}}</label><br>
                            <input type="text" placeholder="{{translate('Ex : 250728969979')}}" class="form-control"
                                name="messagingSenderId" value="{{$firebasemessageconfig['messagingSenderId']}}"
                                required autocomplete="off">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{translate('App ID')}}</label><br>
                            <input type="text"
                                placeholder="{{translate('Ex : 1:250728969979:web:b79642a7b2d2400b75a25e')}}"
                                class="form-control" name="appId" value="{{$firebasemessageconfig['appId']}}" required
                                autocomplete="off">
                        </div>

                        <div class="btn--container justify-content-end">
                            <button type="reset" class="btn btn--reset">{{translate('reset')}}</button>
                            <button type="submit" class="btn btn-primary mb-2 call-demo">{{translate('save')}}
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection