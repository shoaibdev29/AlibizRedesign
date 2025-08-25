<div id="sidebarMain" class="d-none">
    <aside
        class="js-navbar-vertical-aside navbar navbar-vertical-aside navbar-vertical navbar-vertical-fixed navbar-expand-xl navbar-bordered  ">
        <div class="navbar-vertical-container">
            <div class="navbar-vertical-footer-offset">
                <div class="d-flex align-items-center gap-3 py-2 px-3 justify-content-between">

                    @php($logo = Helpers::get_business_settings('logo'))
                    <a class="navbar-brand w-75" href="{{route('branch.dashboard')}}" aria-label="Front">
                        <img class="navbar-brand-logo" src="{{Helpers::onErrorImage(
    $logo,
    asset('storage/app/ecommerce') . '/' . $logo,
    asset('assets/admin/img/160x160/img2.jpg'),
    'ecommerce/'
)}}" alt="{{ translate('Logo') }}">
                    </a>
                    <button type="button" class="js-navbar-vertical-aside-toggle-invoker close mt-1">
                        <i class="tio-first-page navbar-vertical-aside-toggle-short-align"></i>
                        <i class="tio-last-page navbar-vertical-aside-toggle-full-align" title="Expand"></i>
                    </button>
                </div>

                <div class="navbar-vertical-content text-capitalize">
                    <div class="sidebar--search-form py-3">
                        <div class="search--form-group">
                            <button type="button" class="btn"><i class="tio-search"></i></button>
                            <input type="text" class="js-form-search form-control form--control" id="search-bar-input"
                                placeholder="{{ translate('Search Menu...') }}">
                        </div>
                    </div>

                    <ul class="navbar-nav navbar-nav-lg nav-tabs">
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch') ? 'show' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link" href="{{route('branch.dashboard')}}"
                                title="Dashboards">
                                <i class="tio-home-vs-1-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('dashboard')}}
                                </span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{translate('pos')}} {{translate('management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>
                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/pos*') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-shopping nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('POS')}}</span>
                            </a>
                            <ul
                                class="js-navbar-vertical-aside-submenu nav nav-sub {{Request::is('branch/pos*') ? 'd-block' : 'd--none'}}">
                                <li class="nav-item {{Request::is('branch/pos') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.pos.index')}}"
                                        title="{{translate('pos')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('pos')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/pos/orders') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.pos.orders')}}"
                                        title="{{translate('orders')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('orders')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where('branch_id', auth('branch')->id())->Pos()->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle" title="Pages">{{translate('order')}}
                                {{translate('management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/orders*') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:"
                                title="orders">
                                <i class="tio-shopping-cart nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('order')}}
                                </span>
                            </a>
                            <ul
                                class="js-navbar-vertical-aside-submenu nav nav-sub {{Request::is('branch/orders*') ? 'd-block' : 'd--none'}}">
                                <li class="nav-item {{Request::is('branch/orders/list/all') ? 'active' : ''}}">
                                    <a class="nav-link" href="{{route('branch.orders.list', ['all'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('all')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->where(['branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/pending') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['pending'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('pending')}}
                                            <span class="badge badge-soft-info badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'pending', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/confirmed') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['confirmed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('confirmed')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'confirmed', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/processing') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['processing'])}}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('processing')}}
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'processing', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li
                                    class="nav-item {{Request::is('branch/orders/list/out_for_delivery') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['out_for_delivery'])}}"
                                        title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('out_for_delivery')}}
                                            <span class="badge badge-soft-warning badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'out_for_delivery', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/delivered') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['delivered'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('delivered')}}
                                            <span class="badge badge-soft-success badge-pill ml-1">
                                                {{\App\Models\Order::notPos()->where(['order_status' => 'delivered', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/returned') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['returned'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('returned')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'returned', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/orders/list/failed') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['failed'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('failed')}}
                                            <span class="badge badge-soft-danger badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'failed', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>

                                <li class="nav-item {{Request::is('branch/orders/list/canceled') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.orders.list', ['canceled'])}}" title="">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">
                                            {{translate('canceled')}}
                                            <span class="badge badge-soft-dark badge-pill ml-1">
                                                {{\App\Models\Order::where(['order_status' => 'canceled', 'branch_id' => auth('branch')->id()])->count()}}
                                            </span>
                                        </span>
                                    </a>
                                </li>


                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle">{{ translate('product') }} {{ translate('management') }}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/category*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-category nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('category') }}
                                </span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{ Request::is('branch/category*') ? 'block' : 'none' }}">
                                <li class="nav-item {{ Request::is('branch/category/add') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('branch.category.add') }}"
                                        title="{{ translate('add new category') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('category') }}</span>
                                    </a>
                                </li>

                                <li
                                    class="nav-item {{ Request::is('branch/category/add-sub-category') ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('branch.category.add-sub-category') }}"
                                        title="{{ translate('add new sub category') }}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{ translate('sub_category') }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/attribute*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.attribute.add-new') }}">
                                <i class="tio-apps nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('attribute') }}
                                </span>
                            </a>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/product*') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link nav-link-toggle" href="javascript:">
                                <i class="tio-premium-outlined nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('product')}}</span>
                            </a>
                            <ul class="js-navbar-vertical-aside-submenu nav nav-sub"
                                style="display: {{Request::is('branch/product*') ? 'block' : 'none'}}">
                                <li class="nav-item {{Request::is('branch/product/add-new') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.product.add-new')}}"
                                        title="{{translate('add new product')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('add')}} {{translate('new')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/product/list') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.product.list')}}"
                                        title="{{translate('product list')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('list')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/product/bulk-import') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.product.bulk-import')}}"
                                        title="{{translate('bulk import')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_import')}}</span>
                                    </a>
                                </li>
                                <li class="nav-item {{Request::is('branch/product/bulk-export') ? 'active' : ''}}">
                                    <a class="nav-link " href="{{route('branch.product.bulk-export')}}"
                                        title="{{translate('bulk export')}}">
                                        <span class="tio-circle nav-indicator-icon"></span>
                                        <span class="text-truncate">{{translate('bulk_export')}}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{translate('Promotion Management')}}">{{translate('Promotion Management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{ Request::is('branch/banner*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.banner.list') }}">
                                <i class="tio-image nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('banner') }}
                                </span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/flash-sale*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.flash-sale.index') }}">
                                <i class="tio-alarm-alert nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Flash Sale') }}
                                </span>
                            </a>
                        </li>


                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/coupon*') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('branch.coupon.add-new')}}">
                                <i class="tio-gift nav-icon"></i>
                                <span
                                    class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">{{translate('coupon')}}</span>
                            </a>
                        </li>


                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/notification*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.notification.add-new') }}">
                                <i class="tio-notifications nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('send') }} {{ translate('notification') }}
                                </span>
                            </a>
                        </li>




                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{translate('Support & Help Section')}}">{{translate('Support & Help Section')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li class="navbar-vertical-aside-has-menu {{Request::is('branch/message*') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('branch.message.list')}}">
                                <i class="tio-messages nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('messages')}}
                                </span>
                            </a>
                        </li>


                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{translate('report_and_analytics')}}">{{translate('report_and_analytics')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/report/earning') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.report.earning') }}">
                                <i class="tio-chart-pie-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('earning_report') }}
                                </span>
                            </a>
                        </li>


                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/report/order') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.report.order') }}">
                                <i class="tio-chart-bar-2 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('order_report') }}
                                </span>
                            </a>
                        </li>


                        <li
                            class="navbar-vertical-aside-has-menu {{Request::is('branch/report/driver-report') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('branch.report.driver-report')}}">
                                <i class="tio-chart-pie-2 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('deliveryman_report')}}
                                </span>
                            </a>
                        </li>


                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/report/product-report') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.report.product-report') }}">
                                <i class="tio-chart-bar-1 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('product_report') }}
                                </span>
                            </a>
                        </li>


                        <li
                            class="navbar-vertical-aside-has-menu {{Request::is('branch/report/sale-report') ? 'active' : ''}}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{route('branch.report.sale-report')}}">
                                <i class="tio-chart-bar-4 nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{translate('sale_report')}}
                                </span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/report/wallet-transaction-history') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.report.wallet-transaction-history') }}">
                                <i class="tio-wallet nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('wallet_transaction_history') }}
                                </span>
                            </a>
                        </li>


                        <li class="nav-item">
                            <small class="nav-subtitle"
                                title="{{translate('User Management')}}">{{translate('user_management')}}</small>
                            <small class="tio-more-horizontal nav-subtitle-replacer"></small>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/customer/list') || Request::is('branch/customer/view*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.customer.list') }}">
                                <i class="tio-user nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('customer') }} {{ translate('list') }}
                                </span>
                            </a>
                        </li>

                        <li
                            class="navbar-vertical-aside-has-menu {{ Request::is('branch/customer/subscribed-emails*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.customer.subscribed_emails') }}">
                                <i class="tio-email-outlined nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('Subscribed Emails') }}
                                </span>
                            </a>
                        </li>



                        <li class="navbar-vertical-aside-has-menu {{ Request::is('branch/reviews*') ? 'active' : '' }}">
                            <a class="js-navbar-vertical-aside-menu-link nav-link"
                                href="{{ route('branch.reviews.list') }}">
                                <i class="tio-star nav-icon"></i>
                                <span class="navbar-vertical-aside-mini-mode-hidden-elements text-truncate">
                                    {{ translate('product') }} {{ translate('reviews') }}
                                </span>
                            </a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </aside>
</div>

<div id="sidebarCompact" class="d-none">

</div>
@push('script_2')
    <script>
        "use strict"

        $(window).on('load', function () {
            if ($(".navbar-vertical-content li.active").length) {
                $('.navbar-vertical-content').animate({
                    scrollTop: $(".navbar-vertical-content li.active").offset().top - 150
                }, 10);
            }
        });

        let $rows = $('.navbar-vertical-content .navbar-nav > li');
        $('#search-bar-input').keyup(function () {
            var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

            $rows.show().filter(function () {
                var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
                return !~text.indexOf(val);
            }).hide();
        });
    </script>
@endpush