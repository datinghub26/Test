<nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
     id="layout-navbar"
>
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <x-heroicon-o-bars-3 width="24" height="24"/>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        {{--        <div class="navbar-nav align-items-center">--}}
        {{--            <a class="nav-link style-switcher-toggle hide-arrow" href="javascript:void(0);">--}}
        {{--                <i class="ti ti-sm"></i>--}}
        {{--            </a>--}}
        {{--        </div>--}}

        @foreach(\App\Models\NavbarButton::where('is_active', true)->get() as $btn)
            <div class="navbar-nav d-flex justify-content-between align-items-center">
                <a class="btn bg-secondary bg-opacity-50 text-white f-md d-none d-md-block me-2"
                   target="_blank"
                   style="background-color: {{ $btn->bg_color }} !important;"
                   href="{{ $btn->url }}">
                    @if($btn->image)
                        <img src="{{ Storage::url($btn->image) }}" alt="" width="20px" class="me-2">
                    @endif
                    {{ $btn->name }}
                </a>
            </div>
        @endforeach

        <ul class="navbar-nav flex-row align-items-center ms-auto">
            @auth
                <a class="btn bg-primary bg-opacity-50 text-white f-md d-none d-md-block me-2"
                   data-bs-target="#bonusModal"
                   data-bs-toggle="modal"
                   href="javascript:void(0);">
                    <i class="fa-solid fa-inbox me-2" style="font-size: 16px"></i>
                    Redeem Bonus
                </a>

                <livewire:user.notifications/>

                <li class="nav-item navbar-dropdown dropdown me-2">
                    <a style="border-radius: var(--bs-border-radius)"
                       class="nav-link dropdown-toggle hide-arrow d-flex align-items-center justify-content-between bg-secondary bg-opacity-50 text-white px-3 "
                       href="{{ route('shop') }}">

                        <span class="d-flex align-items-center f-md"
                              x-data="{ balance: '{{ round(auth()->user()->points, 2) }}' }"
                              x-on:update-balance.window="balance = $event.detail.balance;">

                            <img src="{{ asset('assets/img/coin.png') }}?v=2" alt="ERC" width="15" height="15" style="object-fit: contain;" class="me-1"
                                 x-show="is_coin == '1'">
                            <i class="fa-solid fa-dollar-sign me-1" x-show="is_coin == '0'"></i>
                            <span
                                x-text="is_coin == '1' ? Number(balance).toLocaleString() + ' ERC' : '$' + Number(balance / 1000).toFixed(2)"></span>


                            <x-heroicon-s-credit-card class=" ms-2" width="20px"/>
                        </span>
                    </a>

                    <div
                        class="d-flex align-items-center justify-content-between bg-secondary bg-opacity-50 text-white rounded-4 px-3"></div>
                </li>

                @if(auth()->user()->isAdmin())
                    <a href="{{ url('/admin') }}" target="_blank" class="mt-1">
                        <i class="fa-solid fa-shield me-2 ms-1 text-body" style="font-size: 20px"></i>
                    </a>
                @endif

                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a
                        class="nav-link dropdown-toggle d-flex align-items-center justify-content-between bg-secondary bg-opacity-50 rounded-pill py-1 pe-3 ps-1 text-body"
                        href="javascript:void(0);"
                        data-bs-toggle="dropdown"
                        data-bs-auto-close="outside">
                        <div class="avatar avatar-online text-start">
                            <img src="{{ auth()->user()->avatar() }}" alt=""/>
                        </div>
                    </a>


                    <ul class="dropdown-menu dropdown-menu-end text-start f-md">
                        <li class="mb-4">
                            <div class=" text-center justify-content-center align-items-center m-auto">
                                <div class="avatar avatar-online d-block m-auto mb-2">
                                    <img src="{{ auth()->user()->avatar() }}" alt
                                         class="h-auto rounded-circle"/>
                                </div>


                                <div class="d-block">
                                    <span class="fw-semibold d-block">{{ auth()->user()->username }}</span>
                                </div>

                                <div class="m-auto" style="max-width: 150px">
                                    <div class="d-flex justify-content-between" style="font-size: 10px">
                                        <span style="margin-left: 3px">lvl {{ auth()->user()->level }}</span>
                                        <span style="margin-right: 3px">lvl {{ auth()->user()->level + 1 }}</span>
                                    </div>

                                    @php
                                        $progress = auth()->user()->levelProgress() ?? 0;
                                    @endphp
                                    <div class="progress bg-dark" style="height: 8px">
                                        <div class="progress-bar" role="progressbar"
                                             style="width: {{ $progress }}%"
                                             aria-valuenow="{{ $progress }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>


                        <li class="">
                            <a class="dropdown-item text-body" href="{{ route('profile') }}">
                                <i class="fa-solid fa-user" style="width: 30px"></i>
                                <span class="align-middle">Profile</span>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item text-body" href="{{ route('referrals') }}">
                                <i class="fa-solid fa-users" style="width: 30px"></i>
                                <span class="align-middle">Affiliates</span>
                            </a>
                        </li>


                        <li>
                            <a class="dropdown-item text-body" href="{{ route('logout') }}">
                                <i class="fa-solid fa-right-from-bracket" style="width: 30px"></i>
                                <span class="align-middle">Logout</span>
                            </a>
                        </li>

                        <li>
                            <div class="mx-2 dropdown-divider"></div>
                        </li>

                        <div class="d-flex justify-content-between align-items-center mx-4 mb-1"
                             x-data="{
                                 isCoin: (typeof localStorage !== 'undefined' ? localStorage.getItem('isCoin') || '0' : '0'),
                                 toggleSwitch() {
                                    this.isCoin = this.isCoin === '1' ? '0' : '1';
                                    localStorage.setItem('isCoin', this.isCoin);
                                    $dispatch('update-coins', { isCoin: this.isCoin });
                                 }
                          }">
                            <span class="switch-label" x-text="isCoin === '1' ? 'Show USD' : 'Show ERC'">Show USD</span>
                            <label class="switch switch-primary f-md">
                                <input type="checkbox" class="switch-input" :checked="isCoin === '0'"
                                       @change="toggleSwitch()"/>
                                <span class="switch-toggle-slider" style="top: 0">
                                <span class="switch-on"></span>
                                <span class="switch-off bg-secondary"></span>
                            </span>
                            </label>
                        </div>
                    </ul>
                </li>
            @else
                <button class="btn bg-primary bg-opacity-50 text-white f-md  d-md-block me-2"
                        data-bs-target="#authModal"
                        data-signup="true"
                        data-bs-toggle="modal">
                    <i class="fa-solid fa-user-plus me-1" style="font-size: 16px"></i>
                    Join Now
                </button>

                <button class="btn bg-secondary bg-opacity-50 text-white f-md d-md-block me-2"
                        data-bs-target="#authModal"
                        data-bs-toggle="modal">
                    <i class="fa-solid fa-arrow-right-to-bracket me-1" style="font-size: 16px"></i>
                    Log In
                </button>
            @endauth
        </ul>
    </div>
</nav>
