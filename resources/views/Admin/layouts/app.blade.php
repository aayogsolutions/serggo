<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <title>@yield('title')</title>
        
        <link rel="stylesheet" href="{{asset('assets/back-end/css/vendor.min.css')}}">
        <!-- <link rel="stylesheet" href="{{asset('assets/back-end/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/back-end/css/google-fonts.css')}}"> -->
        <link rel="stylesheet" href="{{asset('assets/back-end/css/custom.css')}}">
        <!-- <link rel="stylesheet" href="{{asset('assets/back-end/vendor/icon-set/style.css')}}">
        <link rel="stylesheet" href="{{asset('assets/back-end/css/theme.minc619.css?v=1.0')}}">
        <link rel="stylesheet" href="{{asset('assets/back-end/css/daterangepicker.css')}}"> -->
        <link rel="stylesheet" href="{{asset('assets/back-end/css/style.css')}}">

        <link rel="icon" type="image/x-icon" href="{{ asset('Images/Business').'/'.Helpers_get_business_settings('fav_icon') }}">
        <link rel="shortcut icon" href="{{ asset('Images/Business').'/'.Helpers_get_business_settings('fav_icon') }}">
        <link rel="stylesheet" href="{{asset('assets/admin')}}/css/vendor.min.css">
        <link rel="stylesheet" href="{{asset('assets/admin')}}/vendor/icon-set/style.css">
        <link rel="stylesheet" href="{{asset('assets/admin')}}/css/bootstrap.min.css">
        <link rel="stylesheet" href="{{asset('assets/admin/css/owl.min.css')}}">
        <link rel="stylesheet" href="{{asset('assets/admin')}}/css/theme.minc619.css?v=1.0">
        <link rel="stylesheet" href="{{asset('assets/admin')}}/css/style.css">
        
        <script src="{{asset('assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
        <link rel="stylesheet" href="{{asset('assets/admin')}}/css/toastr.css">
        <link rel="stylesheet" href="{{asset('assets/admin')}}/css/custom-helper.css">
        <style>
            .upload--horizontal{
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .upload--horizontal img{
                width: 35%;
                height: 340px;
                object-fit: fill;
                border: 1px solid #000000;
                border-radius: 20px;
            }

            .upload--horizontal video{
                width: 35%;
                height: 340px;
                object-fit: fill;
                border: 1px solid #000000;
                border-radius: 20px;
            }
        </style>
        @stack('css')
    </head>

    <body class="footer-offset">

        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="d-none" id="loading">
                        <div class="loader-image">
                            <img width="200" src="{{asset('assets/admin/img/loader.gif')}}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('Admin.layouts.partials._header')
        @include('Admin.layouts.partials._sidebar')

        <main id="content" role="main" class="main pointer-event">

        @yield('content')

        @include('Admin.layouts.partials._footer')

            <div class="modal fade" id="popup-modal">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h2 class="order-check-colour">
                                            <i class="tio-shopping-cart-outlined"></i> {{translate('You have new order, Check Please.')}}
                                        </h2>
                                        <hr>
                                        <button id="check-order" class="btn btn-primary">{{translate('Ok, let me check')}}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </main>

        <script src="{{asset('assets/admin')}}/js/custom.js"></script>

        <script src="{{asset('assets/admin')}}/js/vendor.min.js"></script>
        <script src="{{asset('assets/admin')}}/js/theme.min.js"></script>
        <script src="{{asset('assets/admin')}}/js/sweet_alert.js"></script>
        <script src="{{asset('assets/admin')}}/js/toastr.js"></script>
        <script src="{{asset('assets/admin/js/owl.min.js')}}"></script>
        <script src="{{asset('assets/back-end/js/vendor.min.js')}}"></script>
        <script src="{{asset('assets/back-end/js/theme.min.js')}}"></script>
        <script src="{{asset('assets/back-end/js/bootstrap.min.js')}}"></script>
        <!-- <script src="{{asset('assets/back-end/js/sweet_alert.js')}}"></script> -->
        <!-- <script src="{{asset('assets/back-end/js/toastr.js')}}"></script> -->
        <script src="{{asset('js/lightbox.min.js')}}"></script>

        <script src="{{asset('assets/back-end/js/moment.min.js')}}"></script>
        <script src="{{asset('assets/back-end/js/daterangepicker.min.js')}}"></script>

        <!-- <script src="{{asset('assets/back-end/js/custom.js')}}"></script> -->
        <!-- <script src="{{asset('assets/back-end/js/app-script.js')}}"></script> -->

        @stack('script')

        @if ($errors->any())
            @foreach($errors->all() as $error)
                <?php
                    flash()->error("{$error}");
                ?>
            @endforeach
        @endif
        <script>
            $(document).on('ready', function () {
                var sidebar = $('.js-navbar-vertical-aside').hsSideNav();

                $('.js-nav-tooltip-link').tooltip({boundary: 'window'})

                $(".js-nav-tooltip-link").on("show.bs.tooltip", function (e) {
                    if (!$("body").hasClass("navbar-vertical-aside-mini-mode")) {
                        return false;
                    }
                });

                $('.js-hs-unfold-invoker').each(function () {
                    var unfold = new HSUnfold($(this)).init();
                });

                $('.js-form-search').each(function () {
                    new HSFormSearch($(this)).init()
                });

                $('.js-select2-custom').each(function () {
                    var select2 = $.HSCore.components.HSSelect2.init($(this));
                });

                $("#choice_tags").select2({
                    tags: true
                });

                $('.js-daterangepicker').daterangepicker();

                $('.js-daterangepicker-times').daterangepicker({
                    timePicker: true,
                    startDate: moment().startOf('hour'),
                    endDate: moment().startOf('hour').add(32, 'hour'),
                    locale: {
                        format: 'M/DD hh:mm A'
                    }
                });
            });
        </script>


        @stack('script_2')
        <audio id="myAudio">
            <source src="{{asset('assets/admin/sound/notification.mp3')}}" type="audio/mpeg">
        </audio>

        <script>
            var audio = document.getElementById("myAudio");

            function playAudio() {
                audio.play();
            }

            function pauseAudio() {
                audio.pause();
            }
        </script>
        <script>
            $('#check-order').on('click', function(){
                location.href = "{{route('admin.test')}}";
            })

            @if(Helpers_module_permission_check('order_management'))
                setInterval(function () {
                    $.get({
                        url: "{{route('admin.test')}}",
                        dataType: 'json',
                        success: function (response) {
                            let data = response.data;
                            if (data.new_order > 0) {
                                playAudio();
                                $('#popup-modal').appendTo("body").modal('show');
                            }
                        },
                    });
                }, 10000);
            @endif

            function route_alert(route, message) {
                Swal.fire({
                    title: '{{translate("Are you sure?")}}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#01684b',
                    cancelButtonText: '{{translate("No")}}',
                    confirmButtonText: '{{translate("Yes")}}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        location.href = route;
                    }
                })
            }

            $('.form-alert').on('click', function (){
                let id = $(this).data('id');
                let message = $(this).data('message');
                form_alert(id, message)
            });

            function form_alert(id, message) {
                Swal.fire({
                    title: '{{translate("Are you sure?")}}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#01684b',
                    cancelButtonText: '{{translate("No")}}',
                    confirmButtonText: '{{translate("Yes")}}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        $('#'+id).submit()
                    }
                })
            }
        </script>

        <script>

            $('.status-change-alert').on('click', function (){
                let url = $(this).data('route');
                let message = $(this).data('message');
                status_change_alert(url, message, event)
            });

            function status_change_alert(url, message, e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ translate("Are you sure?") }}',
                    text: message,
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: 'default',
                    confirmButtonColor: '#107980',
                    confirmButtonText: '{{ translate("Yes") }}',
                    cancelButtonText: '{{ translate("No") }}',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        location.href = url;
                    }
                })
            }
        </script>

        <script>
            var initialImages = [];
            $(window).on('load', function() {
                $("form").find('img').each(function (index, value) {
                    initialImages.push(value.src);
                })
            })

            $(document).ready(function() {
                $('form').on('reset', function(e) {
                    $("form").find('img').each(function (index, value) {
                        $(value).attr('src', initialImages[index]);
                    })
                });
            });
        </script>

        <script>
            $(function(){
                var owl = $('.single-item-slider');
                owl.owlCarousel({
                    autoplay: false,
                    items:1,
                    onInitialized  : counter,
                    onTranslated : counter,
                    autoHeight: true,
                    dots: true,
                });

                function counter(event) {
                    var element   = event.target;         // DOM element, in this example .owl-carousel
                    var items     = event.item.count;     // Number of items
                    var item      = event.item.index + 1;     // Position of the current item

                    if(item > items) {
                        item = item - items
                    }
                    $('.slide-counter').html(+item+"/"+items)
                }
            });
        </script>

        <!-- IE Support -->
        <script>
            if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
        </script>
    </body>
</html>
