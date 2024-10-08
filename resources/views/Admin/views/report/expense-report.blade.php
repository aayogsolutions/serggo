@extends('Admin.layouts.app')

@section('title', translate('Expense Report'))

@section('content')
    <div class="content container-fluid">
        <div class="page-header">
            <div class="media align-items-center">
                <img src="{{asset('assets/admin/img/expense_report.png')}}" class="w--20" alt="">
                <div class="media-body pl-3">
                    <h1 class="page-header-title mb-1">{{translate('expense')}} {{translate('report')}}</h1>
                </div>
            </div>
        </div>

        <div>
            <div class="card">
                <div class="card-header border-0">
                    <div class="w-100 pt-3">
                        <form class="w-100">
                            <div class="row g-3 g-sm-4 g-md-3 g-lg-4">
                                <div class="col-sm-6 col-md-4 col-lg-2">
                                    <div class="input-date-group">
                                        <select class="form-control __form-control" name="date_type" id="date_type">
                                            <option value="this_year" {{ $dateType == 'this_year'? 'selected' : '' }}>{{translate('This Year')}}</option>
                                            <option value="this_month" {{ $dateType == 'this_month'? 'selected' : '' }}>{{translate('This Month')}}</option>
                                            <option value="this_week" {{ $dateType == 'this_week'? 'selected' : '' }}>{{translate('This Week')}}</option>
                                            <option value="custom_date" {{ $dateType == 'custom_date'? 'selected' : '' }}>{{translate('Custom Date')}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3" id="start_date_div">
                                    <div class="input-date-group">
                                        <label class="input-label" for="start_date">{{ translate('Start Date') }}</label>
                                        <label class="input-date">
                                            <input type="text" id="start_date" name="start_date" value="{{$startDate}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="{{ translate('yy-mm-dd')}}" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-3" id="end_date_div">
                                    <div class="input-date-group">
                                        <label class="input-label" for="end_date">{{ translate('End Date') }}</label>
                                        <label class="input-date">
                                            <input type="text" id="end_date" name="end_date" value="{{$endDate}}" class="js-flatpickr form-control flatpickr-custom min-h-45px" placeholder="{{ translate('yy-mm-dd')}}" data-hs-flatpickr-options='{ "dateFormat": "Y-m-d"}'>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-4 col-lg-4 __btn-row">
                                    <a href="{{route('admin.report.expense')}}" id="" class="btn w-100 btn--reset min-h-45px">{{translate('clear')}}</a>
                                    <button type="submit" id="show_filter_data" class="btn w-100 btn--primary min-h-45px">{{translate('show data')}}</button>
                                </div>
                            </div>
                        </form>

                        <div class="col-md-12 pt-4">
                            <div class="report--data">

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="store-report-content mb-2">
                <div class="left-content expense--content">
                    <div class="left-content-card">
                        <img src="{{asset('assets/admin/img/expense.svg')}}" alt="">
                        <div class="info">
                            <h4 class="subtitle">{{ \App\CentralLogics\Helpers::set_symbol($totalExpense) }}</h4>
                            <h6 class="subtext"><span>{{translate('Total_Expense')}}</span></h6>
                        </div>
                    </div>
                    <div class="left-content-card">
                        <img src="{{asset('assets/admin/img/free-delivery.svg')}}" alt="">
                        <div class="info">
                            <h4 class="subtitle">{{ \App\CentralLogics\Helpers::set_symbol($extraDiscount) }}</h4>
                            <h6 class="subtext">{{translate('Extra Discount')}}</h6>
                        </div>
                    </div>
                    <div class="left-content-card">
                        <img src="{{asset('assets/admin/img/free-delivery.svg')}}" alt="">
                        <div class="info">
                            <h4 class="subtitle">{{ \App\CentralLogics\Helpers::set_symbol($freeDelivery) }}</h4>
                            <h6 class="subtext">{{translate('Free_Delivery')}}</h6>
                        </div>
                    </div>
                    <div class="left-content-card">
                        <img src="{{asset('assets/admin/img/coupon-discount.svg')}}" alt="">
                        <div class="info">
                            <h4 class="subtitle">{{ \App\CentralLogics\Helpers::set_symbol($couponDiscount) }}</h4>
                            <h6 class="subtext"><span>{{translate('Coupon_Discount')}}</span></h6>
                        </div>
                    </div>
                </div>
                                    <div class="center-chart-area">
                                        <div class="center-chart-header">
                                            <h3 class="title">{{translate('Transaction Statistics')}}</h3>
                                        </div>
                                        <canvas id="updatingData" class="store-center-chart"
                                                data-hs-chartjs-options='{
                                "type": "bar",
                                "data": {
                                  "labels": [{{ '"'.implode('","', array_keys($expenseTransactionChart['discount_amount'])).'"' }}],
                                  "datasets": [{
                                    "label": "{{\App\CentralLogics\translate('total_expense_amount')}}",
                                    "data": [{{ '"'.implode('","', array_values($expenseTransactionChart['discount_amount'])).'"' }}],
                                    "backgroundColor": "#a2ceee",
                                    "hoverBackgroundColor": "#0177cd",
                                    "borderColor": "#a2ceee"
                                  }]
                                },
                                "options": {
                                  "scales": {
                                    "yAxes": [{
                                      "gridLines": {
                                        "color": "#e7eaf3",
                                        "drawBorder": false,
                                        "zeroLineColor": "#e7eaf3"
                                      },
                                      "ticks": {
                                        "beginAtZero": true,
                                        "fontSize": 12,
                                        "fontColor": "#97a4af",
                                        "fontFamily": "Open Sans, sans-serif",
                                        "padding": 5,
                                        "postfix": " $"
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
                                      },
                                      "categoryPercentage": 0.3,
                                      "maxBarThickness": "10"
                                    }]
                                  },
                                  "cornerRadius": 5,
                                  "tooltips": {
                                    "prefix": " ",
                                    "hasIndicator": true,
                                    "mode": "index",
                                    "intersect": false
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

                <div class="card">
                    <div class="px-3 py-4">
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between">
                            <h4 class="mb-0 mr-auto">
                                {{translate('Total Transactions')}}
                                <span class="badge badge-soft-dark radius-50 fz-12">{{ $expenseTransactionsTable->total() }}</span>
                            </h4>
                            <div class="d-flex flex-wrap gap-3">
                                <form action="{{url()->current()}}" method="GET">
                                    <div class="input-group">
                                        <input type="hidden" name="date_type" value="{{ $dateType }}">
                                        <input type="hidden" name="from" value="{{ $startDate }}">
                                        <input type="hidden" name="to" value="{{ $endDate }}">
                                        <input id="datatableSearch_" type="search" name="search"
                                               class="form-control"
                                               placeholder="{{ translate('Search_by_order_ID')}}"
                                               aria-label="Search"
                                               value="{{$search}}" required autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="submit" class="input-group-text btn--primary">
                                                {{ translate('search') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>


                                <div class="hs-unfold ml-auto">
                                    <a class="js-hs-unfold-invoker btn btn-sm btn-outline-primary-2 dropdown-toggle min-height-40" href="javascript:;"
                                       data-hs-unfold-options='{
                            "target": "#usersExportDropdown",
                            "type": "css-animation"
                            }'>
                                        <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                                    </a>

                                    <div id="usersExportDropdown"
                                         class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{ translate('download') }}
                                {{ translate('options') }}</span>
                                        <a id="export-excel" class="dropdown-item" href="{{ route('admin.report.expense.export.excel', ['search'=>request('search'), 'date_type'=>request('date_type'), 'start_date'=>request('start_date'), 'end_date'=>request('end_date')]) }}">
                                            <img class="avatar avatar-xss avatar-4by3 mr-2"
                                                 src="{{ asset('assets/admin') }}/svg/components/excel.svg"
                                                 alt="{{ translate('excel') }}">
                                            {{ translate('excel') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table __table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table w-100">
                            <thead class="thead-light thead-50 text-capitalize">
                            <tr>
                                <th>{{translate('SL')}}</th>
                                <th>{{translate('Order Date')}}</th>
                                <th>{{translate('Order ID')}}</th>
                                <th class="text-center">{{translate('Expense Amount')}}</th>
                                <th class="text-center">{{translate('Expense Type')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($expenseTransactionsTable as $key=>$transaction)
                                <tr>
                                    <td>{{ $expenseTransactionsTable->firstItem()+$key }}</td>
                                    <td>{{ date_format($transaction->created_at, 'd F Y') }}</td>
                                    <td>{{ $transaction->id }}</td>
                                    <?php
                                        $expense_amount = 0;
                                        if ($transaction->coupon_discount_amount > 0){
                                            $expense_amount = $transaction->coupon_discount_amount;
                                        }elseif ($transaction->extra_discount > 0){
                                            $expense_amount = $transaction->extra_discount;
                                        }elseif ($transaction->free_delivery_amount > 0){
                                            $expense_amount = $transaction->free_delivery_amount;
                                        }
                                    ?>
                                    <td class="text-center">{{ \App\CentralLogics\Helpers::set_symbol($expense_amount) }}</td>
                                    <td class="text-capitalize text-center">
                                        @if(isset($transaction->coupon->coupon_type))
                                            <span class="badge badge-soft-info">
                                                {{translate($transaction->coupon->coupon_type)}}
                                            </span>
                                        @elseif($transaction->free_delivery_amount > 0)
                                            <span class="badge badge-soft-success">
                                            {{translate('Free_Delivery')}}
                                        </span>
                                        @elseif($transaction->extra_discount > 0)
                                            <span class="badge badge-soft-warning">
                                            {{translate('Extra_Discount')}}
                                        </span>
                                        @else
                                            <span class="badge badge-soft-danger">
                                            {{ translate('Coupon Deleted') }}
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="card-footer border-0">
                            <div class="d-flex justify-content-center justify-content-sm-end">
                                {!! $expenseTransactionsTable->links('pagination::bootstrap-4') !!}
                            </div>
                        </div>
                        @if(count($expenseTransactionsTable)==0)
                            <div class="text-center p-4">
                                <img class="mb-3 w-120px" src="{{asset('assets/admin/svg/illustrations/sorry.svg')}}" alt="{{ translate('Image Description')}}">
                                <p class="mb-0">No data to show</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    </div>
@endsection

@push('script_2')

    <script src="{{ asset('assets/admin') }}/js/Chart.min.js"></script>
    <script src="{{ asset('assets/admin') }}/js/chart.js.extensions/chartjs-extensions.js"></script>
    <script src="{{ asset('assets/admin') }}/js/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{asset('assets/admin/js/flatpicker.js')}}"></script>
    <script>
        "use strict";

        $(document).ready(function () {
            $('input').addClass('form-control');
        });

        Chart.plugins.unregister(ChartDataLabels);

        $('.js-chart').each(function() {
            $.HSCore.components.HSChartJS.init($(this));
        });

        var updatingChart = $.HSCore.components.HSChartJS.init($('#updatingData'));

        $('.js-data-example-ajax').select2({
            ajax: {
                url: '{{ url('/') }}/admin/store/get-stores',
                data: function(params) {
                    return {
                        q: params.term,
                        @if (isset($zone))
                        zone_ids: [{{ $zone->id }}],
                        @endif
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                },
                __port: function(params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

    </script>

@endpush
