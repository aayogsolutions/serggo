@extends('Admin.layouts.app')

@section('title', translate('Earning Report'))

@push('css_or_js')
    <style>
        .chartjs-custom{
            height: 18rem;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media align-items-center mb-2">
                <div class="">
                    <img src="{{asset('assets/admin/img/image-4.png')}}" class="w--20" alt="">
                </div>

                <div class="media-body pl-3">
                    <div class="row">
                        <div class="col-lg mb-3 mb-lg-0 text-capitalize">
                            <h1 class="page-header-title">{{translate('earning')}} {{translate('report')}} {{translate('overview')}}</h1>

                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <span>{{translate('admin')}}:</span>
                                    <a href="#"  class="text--primary-2">{{auth('admins')->user()->f_name.' '.auth('admins')->user()->l_name}}</a>
                                </div>

                                <div class="col-auto">
                                    <div class="row align-items-center g-0 m-0">
                                        <div class="col-auto pr-2">{{translate('date :')}}</div>
                                        <div class="text--primary-2">
                                            {{session('from_date')}} - {{session('to_date')}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.report.set-date')}}" method="post">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <div>
                                <label class="form-label mb-0 font-semibold">{{translate('show')}} {{translate('data')}} {{translate('by')}} {{translate('date')}}
                                    {{translate('range')}}</label>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('start')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="from" id="from_date"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <label class="input-label">{{translate('end')}} {{translate('date')}}</label>
                            <label class="input-date">
                                <input type="text" name="to" id="to_date"
                                       class="js-flatpickr form-control flatpickr-custom flatpickr-input" placeholder="{{ translate('dd/mm/yy') }}" required>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="input-label d-none d-md-block">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn--primary min-h-45px btn-block">{{translate('show')}}</button>
                            </div>
                        </div>
                    </div>
                </form>

                <div class="row g-3 mt-3">
                    @php
                        $from = session('from_date');
                        $to = session('to_date');
                        $totalTax=\App\Models\Order::where(['order_status'=>'delivered'])
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('total_tax_amount');

                        if($totalTax==0){
                            $totalTax=0.01;
                        }

                        $totalDeliveryCharge=\App\Models\Order::where(['order_status'=>'delivered'])
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('delivery_charge');

                        if($totalDeliveryCharge==0){
                            $totalDeliveryCharge=0.01;
                        }

                        $totalSold=\App\Models\Order::where(['order_status'=>'delivered'])
                            ->whereBetween('created_at', [$from, $to])
                            ->sum('order_amount');

                        if($totalSold==0){
                            $totalSold=.01;
                        }

                        $totalEarning = $totalSold - $totalTax - $totalDeliveryCharge;
                    @endphp
                    <div class="col-sm-6">
                        <div class="card card-sm bg--2 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <i class="tio-dollar-outlined nav-icon"></i>
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('sold')}}</h4>
                                                <span class="text-success">
                                                <i class="tio-trending-up"></i> {{ Helpers_set_symbol(round(abs($totalSold))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                            "value": {{$totalSold=='.01'?0:round(($totalSold/$totalSold)*100)}},
                                            "maxValue": 100,
                                            "duration": 2000,
                                            "isViewportInit": true,
                                            "colors": ["#00800040", "green"],
                                            "radius": 25,
                                            "width": 3,
                                            "fgStrokeLinecap": "round",
                                            "textFontSize": 14,
                                            "additionalText": "%",
                                            "textClass": "circle-custom-text",
                                            "textColor": "green"
                                            }'>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="card card-sm bg--3 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <i class="tio-money nav-icon"></i>
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('tax')}}</h4>
                                                <span class="text-danger">
                                                <i class="tio-trending-up"></i> {{ Helpers_set_symbol(round(abs($totalTax))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{$totalTax=='0.01'?0:round(((abs($totalTax))/$totalSold)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#f83b3b40", "#f83b3b"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#f83b3b"
                                }'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-sm bg--4 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <i class="tio-money nav-icon"></i>

                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('delivery')}} {{translate('charge') }}</h4>
                                                <span class="text-warning">
                                                <i class="tio-trending-up"></i> {{ Helpers_set_symbol(round(abs($totalDeliveryCharge))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{$totalDeliveryCharge=='0.01'?0:round(((abs($totalDeliveryCharge))/$totalSold)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#ec9a3c40", "#ec9a3c"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#ec9a3c"
                                }'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card card-sm bg--1 border-0 shadow-none">
                            <div class="card-body py-5 px-xl-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="media">
                                            <i class="tio-money nav-icon"></i>
                                            <div class="media-body">
                                                <h4 class="mb-1">{{translate('total')}} {{translate('earning')}}</h4>
                                                <span class="text-warning">
                                                <i class="tio-trending-up"></i> {{ Helpers_set_symbol(round(abs($totalEarning))) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="js-circle"
                                            data-hs-circles-options='{
                                "value": {{$totalEarning=='0.01'?0:round(((abs($totalEarning))/$totalSold)*100)}},
                                "maxValue": 100,
                                "duration": 2000,
                                "isViewportInit": true,
                                "colors": ["#0096ff40", "#0096ff90"],
                                "radius": 25,
                                "width": 3,
                                "fgStrokeLinecap": "round",
                                "textFontSize": 14,
                                "additionalText": "%",
                                "textClass": "circle-custom-text",
                                "textColor": "#0096ff"
                                }'></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                @php
                    $totalSold=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [date('y-01-01'), date('y-12-31')])->sum('order_amount')
                @endphp
                <h6 class="card-subtitle mb-0">{{ translate('Total sale of ') }} {{date('Y')}} :<span
                        class="h3 ml-sm-2"> {{ Helpers_set_symbol($totalSold) }}</span>
                </h6>
            </div>

        @php
            $sold=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $sold[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('order_amount');
                }
        @endphp

        @php
            $tax=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $tax[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('total_tax_amount');
                }
        @endphp

        @php
            $deliveryCharge=[];
                for ($i=1;$i<=12;$i++){
                    $from = date('Y-'.$i.'-01');
                    $to = date('Y-'.$i.'-30');
                    $deliveryCharge[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('delivery_charge');
                }
        @endphp

        @php
            $soldCal=[];
            $taxCal=[];
            $deliveryCharge_cal=[];
            $earning=[];
            for ($i=1;$i<=12;$i++){
                $from = date('Y-'.$i.'-01');
                $to = date('Y-'.$i.'-30');
                $soldCal[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('order_amount');
                $taxCal[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('total_tax_amount');
                $deliveryCharge_cal[$i]=\App\Models\Order::where(['order_status'=>'delivered'])->whereBetween('created_at', [$from, $to])->sum('delivery_charge');
                $earning[$i] = $soldCal[$i] - $taxCal[$i] - $deliveryCharge_cal[$i];
            }
        @endphp

        @php($currency_position = Helpers_get_business_settings('currency_symbol_position'))

            <div class="card-body">
                <div class="chartjs-custom">
                    <canvas class="js-chart"
                            data-hs-chartjs-options='{
                        "type": "line",
                        "data": {
                           "labels": ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                           "datasets": [{
                            "data": [{{$sold[1]}},{{$sold[2]}},{{$sold[3]}},{{$sold[4]}},{{$sold[5]}},{{$sold[6]}},{{$sold[7]}},{{$sold[8]}},{{$sold[9]}},{{$sold[10]}},{{$sold[11]}},{{$sold[12]}}],
                            "backgroundColor": ["rgba(55, 125, 255, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "green",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "green",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#377dff"
                          },

                          {
                            "data": [{{$tax[1]}},{{$tax[2]}},{{$tax[3]}},{{$tax[4]}},{{$tax[5]}},{{$tax[6]}},{{$tax[7]}},{{$tax[8]}},{{$tax[9]}},{{$tax[10]}},{{$tax[11]}},{{$tax[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#f83b3b",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#f83b3b",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#f83b3b"
                          },
                          {
                            "data": [{{$deliveryCharge[1]}},{{$deliveryCharge[2]}},{{$deliveryCharge[3]}},{{$deliveryCharge[4]}},{{$deliveryCharge[5]}},{{$deliveryCharge[6]}},{{$deliveryCharge[7]}},{{$deliveryCharge[8]}},{{$deliveryCharge[9]}},{{$deliveryCharge[10]}},{{$deliveryCharge[11]}},{{$deliveryCharge[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#f5a200",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#f5a200",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#f5a200"
                          },
                          {
                            "data": [{{$earning[1]}},{{$earning[2]}},{{$earning[3]}},{{$earning[4]}},{{$earning[5]}},{{$earning[6]}},{{$earning[7]}},{{$earning[8]}},{{$earning[9]}},{{$earning[10]}},{{$earning[11]}},{{$earning[12]}}],
                            "backgroundColor": ["rgba(0, 201, 219, 0)", "rgba(255, 255, 255, 0)"],
                            "borderColor": "#0096ff",
                            "borderWidth": 2,
                            "pointRadius": 0,
                            "pointBorderColor": "#fff",
                            "pointBackgroundColor": "#0096ff",
                            "pointHoverRadius": 0,
                            "hoverBorderColor": "#fff",
                            "hoverBackgroundColor": "#0096ff"
                          }]
                        },
                        "options": {
                          "gradientPosition": {"y1": 200},
                           "scales": {
                              "yAxes": [{
                                "gridLines": {
                                  "color": "#e7eaf3",
                                  "drawBorder": false,
                                  "zeroLineColor": "#e7eaf3"
                                },
                                "ticks": {
                                  "min": 0,
                                  "max": {{Helpers_max_earning()}},
                                  "stepSize": {{round(Helpers_max_earning()/5)}},
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 10,
                                  "{{ $currency_position == 'left' ? 'prefix' : 'postfix'}}": " {{Helpers_currency_symbol()}}"
                                }
                              }],
                              "xAxes": [{
                                "gridLines": {
                                  "display": false,
                                  "drawBorder": false
                                },
                                "ticks": {
                                  "fontSize": 12,
                                  "fontColor": "#97a4af",
                                  "fontFamily": "Open Sans, sans-serif",
                                  "padding": 5
                                }
                              }]
                          },
                          "tooltips": {
                            "prefix": "",
                            "postfix": "",
                            "hasIndicator": true,
                            "mode": "index",
                            "intersect": false,
                            "lineMode": true,
                            "lineWithLineColor": "rgba(19, 33, 68, 0.075)"
                          },
                          "hover": {
                            "mode": "nearest",
                            "intersect": true
                          }
                        }
                      }'>
                    </canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')

@endpush

@push('script_2')

    <script src="{{asset('assets/admin')}}/vendor/chart.js/dist/Chart.min.js"></script>
    <script
        src="{{asset('assets/admin')}}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js"></script>
    <script src="{{asset('assets/admin')}}/js/hs.chartjs-matrix.js"></script>
    <script src="{{asset('assets/admin/js/flatpicker.js')}}"></script>
    <script src="{{asset('assets/admin/js/earning.js')}}"></script>

    <script>
        $('#from_date,#to_date').change(function () {
            let fr = $('#from_date').val();
            let to = $('#to_date').val();
            if (fr != '' && to != '') {
                if (fr > to) {
                    $('#from_date').val('');
                    $('#to_date').val('');
                    toastr.error('{{ translate("Invalid date range!") }}', Error, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            }
        })
    </script>
@endpush
