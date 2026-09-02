<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <x-logo/>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="fa-solid fa-arrow-left d-none d-xl-block align-middle"></i>
            <x-heroicon-o-x-mark class="d-block d-xl-none align-middle" width="24" height="24"/>
        </a>


    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        @foreach($sidebar as $item)
            @if(isset($item['hide']) && $item['hide'] === true)
                @continue
            @endif

            @if(isset($item['header']))
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ $item['header'] }}</span>
                </li>
            @endif

            @php
                $mainRouteName = $item['route'] ?? null;
                $mainRoute = null;
                if(isset($item['route']))
                    $mainRoute = Route::has($mainRouteName) ? route($mainRouteName) : null;

                $subRoutes = [];
                if(isset($item['submenu']))
                    $subRoutes = array_map(function ($sub) {
                        return $sub['route'];
                    }, $item['submenu']);


                $routeName = Route::currentRouteName();
                $active = $routeName == $mainRouteName || in_array($routeName, $subRoutes);
                $open = $active && isset($item['submenu']);
            @endphp
            <li @class(['menu-item my-2', 'active' => $active, 'open' => $open])>
                <a @class(['menu-link', 'menu-toggle' => isset($item['submenu'])])
                   href="{{ $mainRoute ?? 'javascript:' }}">
                    @svg($item['icon'], 'menu-icon', ['style' => 'font-size: inherit'])
                    {{--                    <i class="menu-icon {{ $item['icon'] }}" style="font-size: inherit"></i>--}}
                    <div data-i18n="{{ $item['name'] }}">{{ $item['name'] }}</div>

                    @if(isset($item['badge']) && $item['badge']['text'])
                        <div
                            class="badge bg-{{ $item['badge']['color'] }} rounded-pill ms-auto">{{ $item['badge']['text'] }}</div>
                    @endif
                </a>


                @if(isset($item['submenu']))
                    <ul class="menu-sub">
                        @foreach($item['submenu'] as $sub)
                            @php
                                $is_active = request()->routeIs($sub['route']) && request()->getRequestUri() == '/' . $sub['route'];
                                if(isset($sub['params'])) {
                                    $is_active = request()->getRequestUri() == '/' . $sub['route'] . "?" . urldecode($sub['params']);
//                                    dd(request()->fullUrl(), route($sub['route']) . "?" . urldecode($sub['params']));
                                    }

                                $url = route($sub['route']);
                                if(isset($sub['params']))
                                    $url .= "?" . urldecode($sub['params']);
                            @endphp
                            <li @class(['menu-item', 'active open' => $is_active])>
                                <a href="{{ $url }}" class="menu-link">
                                    @if(isset($sub['icon']))
                                        @svg($sub['icon'], 'me-2 menu-icon d-block', ['style' => 'width: 22px; height: 22px'])
                                        {{--                                        <i class="me-3 {{ $sub['icon'] }}" style="font-size: inherit"></i>--}}
                                    @endif

                                    <div data-i18n="{{ $sub['name'] }}">{{ $sub['name'] }}</div>

                                    @if(isset($sub['badge']) && $sub['badge']['text'])
                                        <div
                                            class="badge bg-{{ $sub['badge']['color'] }} rounded-pill ms-auto">{{ $sub['badge']['text'] }}</div>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach




        {{--        <x-sidebar-item route="home" name="Home" icon="tf-icons ti ti-home"/>--}}


        {{--        @if(request()->route()->getPrefix() === 'admin')--}}
        {{--            <li class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">--}}
        {{--                <a href="{{ route('admin.dashboard') }}" class="menu-link">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-home"></i>--}}
        {{--                    <div data-i18n="Dashboard">Dashboard</div>--}}
        {{--                </a>--}}
        {{--            </li>--}}

        {{--            <li class="menu-header small text-uppercase">--}}
        {{--                <span class="menu-header-text">Manage Users</span>--}}
        {{--            </li>--}}

        {{--            <li class="menu-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">--}}
        {{--                <a href="{{ route('admin.users') }}" class="menu-link">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-users"></i>--}}
        {{--                    <div data-i18n="Users">Users</div>--}}
        {{--                </a>--}}
        {{--            </li>--}}
        {{--            <li class="menu-item {{ request()->routeIs('admin.online-users') ? 'active' : '' }}">--}}
        {{--                <a href="{{ route('admin.online-users') }}" class="menu-link">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-user-check"></i>--}}
        {{--                    <div data-i18n="Online Users">Online Users</div>--}}
        {{--                </a>--}}
        {{--            </li>--}}

        {{--            <li class="menu-header small text-uppercase">--}}
        {{--                <span class="menu-header-text">Manage Offers</span>--}}
        {{--            </li>--}}

        {{--            <li class="menu-item {{ request()->routeIs('admin.offerwalls') || request()->routeIs('admin.custom-offers') ? 'active open' : '' }}">--}}
        {{--                <a href="javascript:void(0);" class="menu-link menu-toggle">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-tags"></i>--}}
        {{--                    <div data-i18n="Offers">Offers</div>--}}
        {{--                </a>--}}

        {{--                <ul class="menu-sub">--}}
        {{--                    <li class="menu-item {{ request()->routeIs('admin.offerwalls') ? 'active' : '' }}">--}}
        {{--                        <a href="{{ route('admin.offerwalls') }}" class="menu-link">--}}
        {{--                            <div data-i18n="Offerwalls">Offerwalls</div>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    <li class="menu-item {{ request()->routeIs('admin.custom-offers') ? 'active' : '' }}">--}}
        {{--                        <a href="{{ route('admin.custom-offers') }}" class="menu-link">--}}
        {{--                            <div data-i18n="Custom">Custom</div>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                </ul>--}}
        {{--            </li>--}}
        {{--            <li class="menu-item {{ request()->routeIs('admin.promotions') || request()->routeIs('admin.promotions.requests') ? 'active open' : '' }}">--}}
        {{--                <a href="javascript:void(0);" class="menu-link menu-toggle">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-gift"></i>--}}
        {{--                    <div data-i18n="Promotions">Promotions</div>--}}
        {{--                </a>--}}

        {{--                <ul class="menu-sub">--}}
        {{--                    <li class="menu-item {{ request()->routeIs('admin.promotions') ? 'active' : '' }}">--}}
        {{--                        <a href="{{ route('admin.promotions') }}" class="menu-link">--}}
        {{--                            <div data-i18n="Promotions">Promotions</div>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                    <li class="menu-item {{ request()->routeIs('admin.promotions.requests') ? 'active' : '' }}">--}}
        {{--                        <a href="{{ route('admin.promotions.requests') }}" class="menu-link">--}}
        {{--                            <div data-i18n="Requests">Requests</div>--}}
        {{--                        </a>--}}
        {{--                    </li>--}}
        {{--                </ul>--}}
        {{--            </li>--}}

        {{--            <li class="menu-header small text-uppercase">--}}
        {{--                <span class="menu-header-text">Manage Transactions</span>--}}
        {{--            </li>--}}

        {{--            <li class="menu-item {{ request()->routeIs('admin.payments') ? 'active' : '' }}">--}}
        {{--                <a href="{{ route('admin.payments') }}" class="menu-link">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-credit-card"></i>--}}
        {{--                    <div data-i18n="Payments">Payments</div>--}}
        {{--                </a>--}}
        {{--            </li>--}}

        {{--            <li class="menu-item {{ request()->routeIs('admin.postback') ? 'active' : '' }}">--}}
        {{--                <a href="{{ route('admin.postback') }}" class="menu-link">--}}
        {{--                    <i class="menu-icon tf-icons ti ti-transfer-in"></i>--}}
        {{--                    <div data-i18n="Transactions">Transactions</div>--}}
        {{--                </a>--}}
        {{--            </li>--}}

        {{--        @else--}}

        {{--        @endif--}}


    </ul>
</aside>
