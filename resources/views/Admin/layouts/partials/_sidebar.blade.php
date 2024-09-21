<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container text-capitalize">
            <div class="navbar-vertical-footer-offset">
                <div class="navbar-brand-wrapper justify-content-between">

                    @php($logo = Helpers_get_business_settings('logo'))
                    <a class="navbar-brand" href="{{route('admin.dashboard')}}" aria-label="Front">
                        <img class="w-100 side-logo" src="{{ asset('Images/business').'/'.$logo }}" alt="{{ translate('logo') }}"
                            onerror="this.src='{{asset('assets/admin/img/160x160/img1.jpg')}}'">
                    </a>

                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker navbar-vertical-aside-toggle btn btn-icon btn-xs btn-ghost-dark">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                    <div class="navbar-nav-wrap-content-left d-none d-xl-block">
                        <button type="button" class="js-navbar-vertical-aside-toggle-invoker close">
                            <i class="tio-first-page navbar-vertical-aside-toggle-short-align"
                                data-toggle="tooltip" data-placement="right" title="Collapse">
                            </i>
                            <i class="tio-last-page navbar-vertical-aside-toggle-full-align"></i>
                        </button>
                    </div>
                </div>

                <div class="navbar-vertical-content" id="navbar-vertical-content">
                    <form class="sidebar--search-form">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="form-control form--control"
                                placeholder="{{ translate('Search Menu...') }}" id="search-sidebar-menu">
                        </div>
                    </form>
                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['dashboard_management']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin')?'show active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('admin.dashboard')}}" title="{{translate('dashboard')}}">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('dashboard')}}
                                </span>
                            </a>
                        </li>
                        @endif

                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['order_management']))
                        <li class="nav-item">
                            <small
                                class="nav-subtitle">{{translate('order_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['manage_order']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/orders*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{translate('orders')}}">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('orders')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/order*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/orders/list/all')?'active':''}}">
                                    <a class="nav-link" href=""
                                        title="{{translate('all_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>{{translate('all')}}</span>
                                            <span class="badge badge-info badge-pill ml-1">
                                                23
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/pending')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('pending_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>{{translate('pending')}}</span>
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                62
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/confirmed')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('confirmed_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate sidebar--badge-container">
                                            <span>{{translate('confirmed')}}</span>
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                01
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/processing')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('processing_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate  sidebar--badge-container">
                                            <span>{{translate('packaging')}}</span>
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                06
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/out_for_delivery')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('out_for_delivery_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate  sidebar--badge-container">
                                            <span>{{translate('out_for_delivery')}}</span>
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                05
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/delivered')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('delivered_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate  sidebar--badge-container">
                                            <span>{{translate('delivered')}}</span>
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                25
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/returned')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('returned_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate  sidebar--badge-container">
                                            <span>{{translate('returned')}}</span>
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                00
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/orders/list/failed')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('failed_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate  sidebar--badge-container">
                                            <span>{{translate('failed')}}</span>
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                10
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/orders/list/canceled')?'active':''}}">
                                    <a class="nav-link " href=""
                                        title="{{translate('canceled_orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate  sidebar--badge-container">
                                            <span>{{translate('canceled')}}</span>
                                            <span class="badge badge-soft-light badge-pill ml-1">
                                                15
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @endif

                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['product_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('product_management')}} </small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['brand_setup']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/brands*') ?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('brands')}}">
                                        <i class="tio-category nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('brands')}}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/brands*')?'block':'none'}}">
                                        <li class="nav-item {{Request::is('admin/brands/add')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.brands.add')}}" title="{{translate('brands')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('brands')}}
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['category_setup']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/category*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('category setup')}}">
                                        <i class="tio-category nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('category setup')}}</span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/category*')?'block':'none'}}">
                                        <li class="nav-item {{Request::is('admin/category/add')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.category.add')}}" title="{{translate('categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('categories')}}
                                                </span>
                                            </a>
                                        </li>

                                        <li class="nav-item {{Request::is('admin/category/add-sub-category')?'active':''}}">
                                            <a class="nav-link " href="{{route('admin.category.add-sub-category')}}" title="{{translate('sub_categories')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('sub_categories')}}
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['product_setup']))
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/product*') || Request::is('admin/attribute*')?'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                    href="javascript:"
                                    title="{{translate('product setup')}}">
                                    <i class="tio-premium-outlined nav-icon"></i>
                                    <span
                                        class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('product setup')}}</span>
                                </a>
                                <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                    style="display: {{Request::is('admin/product*') || Request::is('admin/attribute*') ? 'block' : 'none'}}">

                                    <li class="nav-item {{Request::is('admin/attribute*')?'active':''}}">
                                        <a class="nav-link"
                                            href="{{route('admin.attribute.add-new')}}"
                                            title="{{translate('product attribute')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('product attribute')}}</span>
                                        </a>
                                    </li>

                                    <li class="nav-item {{Request::is('admin/product/list*')?'active':''}} {{Request::is('admin/product/add-new')?'active':''}}">
                                        <a class="nav-link " href="{{route('admin.product.list')}}"
                                            title="{{translate('list')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('product list')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/product/bulk-import')?'active':''}}">
                                        <a class="nav-link " href="{{('admin.product.bulk-import')}}"
                                            title="{{translate('bulk_import')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('bulk_import')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/product/bulk-export-index')?'active':''}}">
                                        <a class="nav-link " href="{{('admin.product.bulk-export-index')}}"
                                            title="{{translate('bulk_export')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('bulk_export')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item {{Request::is('admin/product/limited-stock')?'active':''}}">
                                        <a class="nav-link" href="{{('admin.product.limited-stock')}}"
                                            title="{{translate('Limited Stocks')}}">
                                            <span class="tio-circle nav-indicator-icon"></span>
                                            <span class="text-truncate">{{translate('Limited Stocks')}}</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['product_approval']))
                            <li class="navbar-vertical-aside-has-menu {{Request::is('admin/approv-product') ? 'active':''}}">
                                <a class="js-navbar-vertical-aside-menu-link nav-link" href="javascript:" title="{{translate('Approval Request')}}">
                                    <i class="tio-category nav-icon"></i>
                                    <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Approval Request')}}</span>
                                </a>
                            </li>
                            @endif
                        @endif

                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['promotion_management']))
                            <li class="nav-item">
                                <small
                                    class="nav-subtitle">{{translate('promotion_management')}} </small>
                                <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                            </li>
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['banner']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/banners*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:" title="{{translate('banners setup')}}">
                                        <i class="tio-premium-outlined nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('banners setup')}}
                                        </span>
                                    </a>
                                    <ul class="js-navbar-vertical-aside-submenu nav nav-sub" style="display: {{Request::is('admin/banners*') ? 'block' : 'none'}}">
                                        <li class="nav-item {{Request::is('admin/banners/splash*')?'active':''}}">
                                            <a class="nav-link" href="{{route('admin.banners.splash.add')}}" title="{{translate('Splash Screens')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('splash screens')}}
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/banners/auth*')?'active':''}}">
                                            <a class="nav-link" href="{{route('admin.banners.auth.add')}}" title="{{translate('User Auth Screens')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('Login & Signup')}}
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/banners/home/*')?'active':''}}">
                                            <a class="nav-link" href="{{route('admin.banners.home.add')}}" title="{{translate('User Home Screens')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('Home Banner')}}
                                                </span>
                                            </a>
                                        </li>
                                        <li class="nav-item {{Request::is('admin/banners/homeslider*')?'active':''}}">
                                            <a class="nav-link" href="{{route('admin.banners.homeslider.add')}}" title="{{translate('User Home silder Screens')}}">
                                                <span class="tio-circle nav-indicator-icon"></span>
                                                <span class="text-truncate">
                                                    {{translate('Home silder Banner')}}
                                                </span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['display']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/display*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('admin.display.add')}}" title="{{translate('display sections')}}">
                                        <i class="tio-premium-outlined nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('display sections')}}
                                        </span>
                                    </a>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['coupons']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/coupon*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                                        href="{{('admin.coupon.add-new')}}"
                                        title="{{translate('coupons')}}">
                                        <i class="tio-gift nav-icon"></i>
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('coupons')}}</span>
                                    </a>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['send_notification']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/notification*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                                        href="{{('admin.notification.add-new')}}"
                                        title="{{translate('send notifications')}}">
                                        <i class="tio-notifications nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('send')}} {{translate('notifications')}}
                                        </span>
                                    </a>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['offers']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/offer*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link"
                                        href="{{('admin.offer.flash.index')}}"
                                        title="{{translate('offers')}}">
                                        <i class="tio-alarm-alert nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('offers')}}
                                        </span>
                                    </a>
                                </li>
                            @endif
                            @if(Helpers_module_permission_check(MANAGEMENT_SECTION['category_discount']))
                                <li class="navbar-vertical-aside-has-menu {{Request::is('admin/discount*')?'active':''}}">
                                    <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{('admin.discount.add-new')}}"
                                        title="{{translate('category_discount')}}">
                                        <i class="tio-layers-outlined nav-icon"></i>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('category_discount')}}</span>
                                    </a>
                                </li>
                            @endif
                        @endif

                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['report_management']))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="Documentation">{{translate('report_and_analytics')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['sales_report']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/sale-report')?'active':''}}">
                            <a class="nav-link " href="{{('admin.report.sale-report')}}"
                                title="{{translate('sale')}} {{translate('report')}}">
                                <span class="tio-chart-bar-1 nav-icon"></span>
                                <span class="text-truncate">{{translate('Sales Report')}}</span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['order_report']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/order')?'active':''}}">
                            <a class="nav-link " href="{{('admin.report.order')}}"
                                title="{{translate('order')}} {{translate('report')}}">
                                <span class="tio-chart-bar-2 nav-icon"></span>
                                <span class="text-truncate">{{translate('Order Report')}}</span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['earning_report']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/report/earning')?'active':''}}">
                            <a class="nav-link " href="{{('admin.report.earning')}}"
                                title="{{translate('earning')}} {{translate('report')}}">
                                <span class="tio-chart-pie-1 nav-icon"></span>
                                <span
                                    class="text-truncate">{{translate('earning')}} {{translate('report')}}</span>
                            </a>
                        </li>
                        @endif
                        @endif

                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['user_management']))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="Documentation">{{translate('user management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['customer_list']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/list') || Request::is('admin/customer/view*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{('admin.customer.list')}}"
                                title="{{translate('customer')}} {{translate('list')}}">
                                <i class="tio-poi-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('customer')}} {{translate('list')}}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['vender_list']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/loyalty-point*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="javascript:" title="{{translate('vender list')}}">
                                <i class="tio-medal nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('vender list')}}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['serviceman_list']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/loyalty-point*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="javascript:" title="{{translate('service men list')}}">
                                <i class="tio-medal nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('service men list')}}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['coustomer_wallet']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/customer/wallet/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{translate('Customer Wallet')}}">
                                <i class="tio-wallet-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Customer Wallet')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/customer/wallet*')?'block':'none'}}">

                                <li class="nav-item {{Request::is('admin/customer/wallet/add-fund')?'active':''}}">
                                    <a class="nav-link"
                                        href="{{('admin.customer.wallet.add-fund')}}"
                                        title="{{translate('add_fund')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('add_fund')}}
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/customer/wallet/report')?'active':''}}">
                                    <a class="nav-link"
                                        href="{{('admin.customer.wallet.report')}}"
                                        title="{{translate('report')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('report')}}
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/customer/wallet/bonus*')?'active':''}}">
                                    <a class="nav-link"
                                        href="{{('admin.customer.wallet.bonus.index')}}"
                                        title="{{translate('wallet_bonus_setup')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('wallet_bonus_setup')}}
                                        </span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['product_reviews']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/reviews*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{('admin.reviews.list')}}"
                                title="{{translate('product')}} {{translate('reviews')}}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('product')}} {{translate('reviews')}}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['employees']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/employee*')?'active':''}}  {{Request::is('admin/custom-role*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:" title="{{translate('employees')}}">
                                <i class="tio-incognito nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('employees')}}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/employee*') || Request::is('admin/custom-role*') || Request::is('admin/employee/update/*') ?'block':'none'}}">

                                <li class="nav-item {{Request::is('admin/custom-role*')?'active':''}}">
                                    <a class="nav-link"
                                        href="{{route('admin.custom-role.create')}}"
                                        title="{{translate('Employee Role Setup')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                            {{translate('Employee Role Setup')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/employee/list')?'active':''}}">
                                    <a class="nav-link" href="{{route('admin.employee.list')}}"
                                        title="{{translate('List')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Employee List')}}</span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('admin/employee/add-new')?'active':''}}">
                                    <a class="nav-link " href="{{route('admin.employee.add-new')}}"
                                        title="{{translate('add_new')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('Add New Employee')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @endif

                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['system_management']))
                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="Layouts">{{translate('system setting')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['business_setup']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/store*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('admin.business-settings.store.ecom-setup')}}"
                                title="{{translate('Business Setup')}}">
                                <i class="tio-settings nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('Business Setup')}}
                                </span>
                            </a>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['branch_setup']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/branch*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:"
                                title="{{translate('Branch Setup')}}">
                                <i class="tio-shop nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Branch Setup')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/branch*')?'block':'none'}}">
                                <li class="nav-item {{Request::is('admin/branch/add-new')?'active':''}}">
                                    <a class="nav-link " href="{{('admin.branch.add-new')}}"
                                        title="{{translate('add New')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('Add New')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/branch/list')?'active':''}}">
                                    <a class="nav-link " href="{{('admin.branch.list')}}"
                                        title="{{translate('list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if(Helpers_module_permission_check(MANAGEMENT_SECTION['pages_media']))
                        <li class="navbar-vertical-aside-has-menu {{Request::is('admin/business-settings/page-setup/*')?'active':''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle"
                                href="javascript:"
                                title="{{translate('Pages & Media')}}">
                                <i class="tio-pages-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Pages & Media')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('admin/business-settings/page-setup/*')?'block':''}} {{Request::is('admin/business-settings/web-app/third-party/social-media')?'block':''}}">
                                <li class="nav-item mt-0 {{Request::is('admin/business-settings/page-setup/*')?'active':''}}">
                                    <a class="nav-link"
                                        href="{{('admin.business-settings.page-setup.about-us')}}"
                                        title="{{translate('Page Setup')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('Page Setup')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('admin/business-settings/web-app/third-party/social-media')?'active':''}}">
                                    <a class="nav-link "
                                        href="{{('admin.business-settings.web-app.third-party.social-media')}}"
                                        title="{{translate('Social Media')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span
                                            class="text-truncate">{{translate('Social Media')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @endif
                        <li class="nav-item">
                            <div class="nav-divider"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>


@push('script_2')
<script>
    $(window).on('load', function() {
        if ($(".navbar-vertical-content li.active").length) {
            $('.navbar-vertical-content').animate({
                scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
            }, 10);
        }
    });

    var $rows = $('#navbar-vertical-content .navbar-nav > li');
    $('#search-sidebar-menu').keyup(function() {
        var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

        $rows.show().filter(function() {
            var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
    });
</script>
@endpush