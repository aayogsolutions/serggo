<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Admin | Login</title>
    
    <link rel="icon" type="image/x-icon" href="">

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('assets/admin/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('assets/admin/css/toastr.css')}}">
</head>

<body>
<main id="content" role="main" class="main">
    <div class="auth-wrapper">
        <div class="auth-wrapper-left">
            <div class="auth-left-cont">
            @php($logo = Helpers_get_business_settings('logo'))
                <img src="{{ asset('Images/business').'/'.$logo }}" alt="{{ translate('logo') }}"
                onerror="this.src='{{asset('assets/admin/img/400x400/img2.jpg')}}'">
                <h2 class="title">Your <span class="d-block">All Fresh Food</span> <strong class="text--039D55">in one Place....</strong></h2>
            </div>
        </div>
        <div class="auth-wrapper-right">
            <div class="auth-wrapper-form">
                <form id="form-id" action="{{ route('admin.login.submit') }}" method="post">
                    @csrf
                    <div class="auth-header">
                        <div class="mb-5">
                            <div class="auth-wrapper-right-logo">
                                <img src="" alt=" logo ">
                            </div>
                            <h2 class="title">SERGGO</h2>
                            <div>Welcome Back</div>
                            
                        </div>
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label text-capitalize"
                            for="signinSrEmail">your email</label>

                        <input type="email" class="form-control form-control-lg" name="email" id="signinSrEmail"
                            tabindex="1" placeholder="email@address.com" aria-label="email@address.com"
                            required data-msg="Please enter a valid email address.">
                    </div>

                    <div class="js-form-message form-group">
                        <label class="input-label" for="signupSrPassword" tabindex="0">
                            <span class="d-flex justify-content-between align-items-center">
                            password
                            </span>
                        </label>

                        <div class="input-group input-group-merge">
                            <input type="password" class="js-toggle-password form-control form-control-lg"
                                name="password" id="signupSrPassword" placeholder="8+ characters required"
                                aria-label="8+ characters required" required
                                data-msg="Your password is invalid. Please try again."
                                data-hs-toggle-password-options='{
                                            "target": "#changePassTarget",
                                    "defaultClass": "tio-hidden-outlined",
                                    "showClass": "tio-visible-outlined",
                                    "classChangeTarget": "#changePassIcon"
                                    }'>
                            <div id="changePassTarget" class="input-group-append">
                                <a class="input-group-text" href="javascript:">
                                    <i id="changePassIcon" class="tio-visible-outlined"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox d-flex align-items-center">
                            <input type="checkbox" class="custom-control-input" id="termsCheckbox"
                                name="remember">
                            <label class="custom-control-label text-muted m-0" for="termsCheckbox">
                                remember me 
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-block btn--primary">login</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>

<script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('assets/admin')}}/js/toastr.js"></script>


<script>
    $(document).on('ready', function () {
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });

        $('.js-validate').each(function () {
            $.HSCore.components.HSValidation.init($(this));
        });
    });

    $.getJSON('https://api.ipify.org?format=json', function(data) {
        var ipAddress = data.ip;
        console.log('User IP address: ' + ipAddress);
        console.log(data);
    });
</script>

@if(isset($recaptcha) && $recaptcha['status'] == 1)
    <script type="text/javascript">
        var onloadCallback = function () {
            grecaptcha.render('recaptcha_element', {
                'sitekey': '{{ Helpers::get_business_settings('recaptcha')['site_key'] }}'
            });
        };
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
    <script>
        $("#form-id").on('submit',function(e) {
            var response = grecaptcha.getResponse();

            if (response.length === 0) {
                e.preventDefault();
                toastr.error("Please check the recaptcha");
            }
        });
    </script>
@else
    <script type="text/javascript">
        $('.refresh-recaptcha').on('click', function() {
            reCaptcha();
        });
        function reCaptcha() {
            var $url = "{{ URL('/admin/auth/code/captcha') }}";
            var $url = $url + "/" + Math.random();
            document.getElementById('default_recaptcha_id').src = $url;
        }
    </script>
@endif

@if(env('APP_MODE')=='demo')
    <script>
        $('#copyButton').on('click', function() {
            copyCredentials();
        });

        function copyCredentials() {
            $('#signinSrEmail').val('admin@admin.com');
            $('#signupSrPassword').val('12345678');
            toastr.success('Copied successfully!', 'Success!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
@endif
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
