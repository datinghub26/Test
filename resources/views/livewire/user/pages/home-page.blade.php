@assets
<style>
    .home-offer-container {
        max-width: 100%;
        display: flex;
        gap: 1rem;
    }

    @media (max-width: 514px) {
        .home-offer-container {
            justify-items: center;
            gap: 1rem;
        }
    }

    @keyframes popIn {
        from {
            transform: scale(0.85);
            opacity: 0.01;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .offer-card {
        animation: popIn 0.5s ease-in-out;
        border-radius: 10px;
        transition: all 0.3s;
        box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);
        padding: 10px !important;
        cursor: pointer;
        border: none;
    }

    .offer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.2);
    }

    .offer-card {
        width: 10rem;
    }

    .partner-slider {
        overflow: hidden;
        position: relative;
        width: 100%;
        white-space: nowrap;
    }

    .partner-track {
        display: flex;
        align-items: center;
        gap: 40px;
        width: max-content;
        animation: scroll 30s linear infinite;
    }

    .partner-logo img {
        max-height: 80px;
        max-width: 200px;
        opacity: 0.75;
        transition: transform 0.3s ease;
    }

    /* Hover effect */
    .partner-logo img:hover {
        transform: scale(1.1);
        opacity: 1;
    }

    /* Smooth Scrolling Animation */
    @keyframes scroll {
        from {
            transform: translateX(0);
        }
        to {
            transform: translateX(-50%);
        }
    }

</style>
@endassets

<div>
    <div class="col-12">
        <div class="d-flex justify-content-center fw-bolder text-center"
             style="font-size: 3rem; color: #fff; padding-top: 5rem">
            <div>
                <span class="text-primary">Earn rewards.</span> testing apps, <br>anywhere & anytime.
                <div class="d-flex justify-content-center gap-2 row-gap-1 flex-wrap align-items-center mt-3"
                     style="font-size: 14px; text-transform: none; color: #636363">
                    <div class="d-flex align-items-center">
                        <span class="ms-1">Earn money online up to <strong class="text-white">$14.99</strong> per offer</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-circle text-primary" style="font-size: 9px"></i>
                        <span class="ms-1">
                            <strong class="text-white"
                                    wire:init="loadAvailableOffersCount">{{ $this->availableOffersCount }}</strong> available offers now
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{--  Main Section  --}}
        <div class="d-flex flex-column flex-xxl-row mt-5 justify-content-center"
             style="row-gap: 6rem; column-gap: 4rem;">
            <div class="">
                <div class="d-flex mt-4">
                    <div class="mt-4 home-offer-container justify-items-center m-auto">
                        @forelse($topOffers as $offer)
                            @php
                                $price = to_money($offer->points);
                                $priceFormatted = number_format($price, 2);
                                $priceParts = explode('.', $priceFormatted);
                            @endphp
                            <a class="card bg-dark bg-opacity-75 offer-card p-2"
                               data-bs-target="#authModal" data-bs-toggle="modal">
                                <div class="card-img-block text-center align-items-center d-flex justify-content-center">
                                    <img class="card-img-top rounded-3" style="width: 100%; height: 100%; object-fit: cover"
                                         src="{{ $offer->image }}" alt="{{ $offer->title }}">
                                    <i class="fa-solid fa-play position-absolute text-white text-primary rounded-circle"
                                       style="background: rgba(var(--bs-primary-rgb), 0.30) !important"></i>
                                </div>
                                <div class="card-body p-0 pt-0 pb-3 small text-start">
                                    <span class="text-truncate text-white d-block mt-2">{{ $offer->title }}</span>
                                    <span class="text-truncate text-secondary d-block">{{ $offer->description ?? "Complete offers on " . config('app.name') . " and earn a lot!" }}</span>
                                </div>
                                <div class="card-footer justify-content-between border-0 pt-0 pb-2 p-0 text-start">
                                    <div class="f-w-600 text-white">
                                        <span class="d-flex align-items-end fw-bold"
                                              style="font-size: 17px; line-height: 16px">${{ $priceParts[0] }}.<span
                                                style="font-size: 15px; line-height: 12px"
                                                class="fw-bold">{{ $priceParts[1] ?? '00' }}</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <a class="card bg-dark bg-opacity-75 offer-card p-2"
                               data-bs-target="#authModal" data-bs-toggle="modal">
                                <div class="card-img-block text-center align-items-center d-flex justify-content-center">
                                    <img class="card-img-top rounded-3" style="width: 100%; height: 100%; object-fit: cover"
                                         src="https://main-p.agmcdn.com/offers/671451-swwPrk5B.jpg" alt="">
                                    <i class="fa-solid fa-play position-absolute text-white text-primary rounded-circle"
                                       style="background: rgba(var(--bs-primary-rgb), 0.30) !important"></i>
                                </div>
                                <div class="card-body p-0 pt-0 pb-3 small text-start">
                                    <span class="text-truncate text-white d-block mt-2">Monopoly Go!</span>
                                    <span
                                        class="text-truncate text-secondary d-block">Complete offers on {{ config('app.name') }} and earn a lot!</span>
                                </div>
                                <div class="card-footer justify-content-between border-0 pt-0 pb-2 p-0 text-start">
                                    <div class="f-w-600 text-white">
                                        <span class="d-flex align-items-end fw-bold"
                                              style="font-size: 17px; line-height: 16px">$46.<span
                                                style="font-size: 15px; line-height: 12px"
                                                class="fw-bold">97</span>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="">
                <div class="card m-auto" style="width: 30rem; border-radius: 20px; max-width: 100%">
                    <div class="card-body" style="padding: 1.5rem !important;">
                        <div class="text-center">
                            <span class="text-body fs-4 fw-bold">Get Started!</span>
                            <p class="text-secondary fw-medium">It's free! Sign up and start to earn money!</p>
                        </div>

                        <div class="mb-3 fw-medium"
                             x-data="{ email: '', password: '', loading: false, showPassword: false }">
                            <div class="mb-3">
                                <label class="form-label text-body">Email</label>
                                <input class="form-control" type="text" x-model="email" name="email"
                                       placeholder="Enter your email">
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label text-body">Password</label>
                                    <a href="javascript:" data-bs-target="#forgetPasswordModal" data-bs-toggle="modal">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge has-validation">
                                    <input class="form-control" type="password" name="password" x-model="password"
                                           x-bind:type="showPassword ? 'text' : 'password'"
                                           placeholder="············" aria-describedby="password"
                                           autocomplete="current-password">
                                    <span class="input-group-text cursor-pointer"
                                          x-on:click="showPassword = !showPassword">
                                        <x-heroicon-o-eye-slash x-show="!showPassword" class="text-body" width="20px"/>
                                        <x-heroicon-o-eye x-show="showPassword" class="text-body" width="20px"/>
                                    </span>
                                </div>
                            </div>
                            
                            
                            <div class="mb-3">
                             <label>
                                    <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div>
                                        <span class="text-danger" id="captchaError"></span>
                                   </label>
                                   
                                   </div>
                                   
                                   
                            <div class="mb-3">
                                <div class="form-check form-label text-body">
                                    <input class="form-check-input" type="checkbox" name="remember"
                                           aria-label="Remember Me">
                                    <label class="form-check-label">Remember Me</label>
                                </div>
                            </div>
                            <button class="btn btn-primary d-flex align-items-center w-100 waves-effect waves-light"
                                    :class="loading && 'disabled'"
                                    @click="loading = true; $dispatch('register', { email: email, password: password }); "
                                    x-on:register-finished.window="console.log('1212'); loading = false">
                                Start Earning Now
                            </button>

                            <div class="divider my-2">
                                <div class="divider-text">or</div>
                            </div>

                            <a href="{{ route('auth.google') }}"
                               class="btn btn-dark d-flex align-items-center w-100 waves-effect waves-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 28 28"
                                     fill="none">
                                    <g clip-path="url(#clip0_436_15312)">
                                        <path
                                            d="M6.20539 16.9208L5.23075 20.5592L1.66846 20.6346C0.603859 18.66 0 16.4008 0 14C0 11.6785 0.564594 9.48921 1.56537 7.56153L4.73758 8.14297L6.12686 11.2954C5.83609 12.1431 5.6776 13.0531 5.6776 14C5.67771 15.0277 5.86387 16.0123 6.20539 16.9208Z"
                                            fill="#FFC700"></path>
                                        <path
                                            d="M27.7554 11.3846C27.9162 12.2315 28 13.1061 28 14C28 15.0023 27.8946 15.98 27.6939 16.9231C27.0123 20.1323 25.2316 22.9346 22.7647 24.9177L22.7639 24.9169L18.7693 24.7131L18.2039 21.1839C19.8408 20.2239 21.1201 18.7216 21.794 16.9231H14.3078V11.3846H27.7554Z"
                                            fill="#518EF8"></path>
                                        <path
                                            d="M22.7639 24.9169L22.7647 24.9177C20.3655 26.8461 17.3177 28 14 28C8.66846 28 4.03309 25.02 1.66846 20.6346L6.20539 16.9208C7.38768 20.0761 10.4315 22.3223 14 22.3223C15.5338 22.3223 16.9709 21.9077 18.2039 21.1839L22.7639 24.9169Z"
                                            fill="#DD2E18"></path>
                                        <path
                                            d="M22.9362 3.22306L18.4008 6.93613C17.1246 6.13845 15.6161 5.67766 14 5.67766C10.3508 5.67766 7.24992 8.02687 6.12686 11.2954L1.56537 7.56153C3.89539 3.06923 8.58922 0 14 0C17.3969 0 20.5115 1.21002 22.9362 3.22306Z"
                                            fill="#C34646"></path>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_436_15312">
                                            <rect width="28" height="28" fill="white"></rect>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <span class="ms-2">Sign up via Google</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="container">

            {{--  Statistics  --}}
            <div class="d-flex justify-content-center" style="margin-top: 12rem;">
                <div class="col-md-12 " style="background: var(--bs-base); border-radius: 20px">
                    <div class="row justify-content-center row-gap-3" style=" ">
                        <div class="col-md-3 text-center p-3">
                            <h3 class="mt-1 fw-bold"><i
                                    class="fa-solid fa-clock fs-3 me-2"></i>{{ $statistics['userLatestCashoutTimeAvg'] }}
                            </h3>
                            <p>Average Time Until First Cashout</p>
                        </div>
                        <div class="col-md-3 text-center p-3">
                            {{--                        <i class="fa-solid fa-sack-dollar fs-4"></i>--}}
                            <h3 class="mt-1 fw-bold">${{ $statistics['totalEarnedYesterday'] }}</h3>
                            <p>Yesterday’s Per-User Earning Performance</p>
                        </div>
                        <div class="col-md-3 text-center p-3">
                            {{--                        <i class="fa-solid fa-sack-dollar fs-4"></i>--}}
                            <h3 class="mt-1 fw-bold">${{ $statistics['totalEarned'] }}</h3>
                            <p>Total Revenue Generated by {{ config('app.name') }}
                        </div>
                    </div>
                </div>
            </div>

            {{--  How it works  --}}
            <div class="row justify-content-center" style="margin-top: 6rem;">
                <div class="text-center">
                    <h2 class="text-primary fw-bolder">Your simple path to extra income</h2>
                    <p class="text-body">Complete easy tasks in your spare time and start earning today. Join {{ config('app.name') }} and turn every moment into a rewarding opportunity</p>
                </div>

                <div class="col-md-12 col-lg-12 rounded">
                    <div class="row justify-content-center row-gap-3">
                        <div class="col-md-4">
                            <div class="card h-100" style="border-radius: 25px;">
                                <div
                                    class="card-body px-0 d-flex flex-column align-items-center justify-content-center gap-1 text-center">
                                    <x-svg.user-shield class="mb-2 pop-in"/>

                                    <p class="text-white fw-medium fs-4">Join In</p>
                                    <p class="text-secondary" style="max-width: 95%">
                                        Create your free account to start earning
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100" style="border-radius: 25px;">
                                <div
                                    class="card-body px-0 d-flex flex-column align-items-center justify-content-center gap-1 text-center">
                                    <x-svg.check-list class="mb-2 pop-in"/>
                                    <p class="text-white fw-medium fs-4">Select Task</p>
                                    <p class="text-secondary" style="max-width: 95%">
                                        Select Task and complete them quickly.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100" style="border-radius: 25px;">
                                <div
                                    class="card-body px-0 d-flex flex-column align-items-center justify-content-center gap-1 text-center">
                                    <x-svg.wallet class="mb-2 pop-in"/>
                                    <p class="text-white fw-medium fs-4">Withdraw</p>
                                    <p class="text-secondary" style="max-width: 95%">
                                        Get paid fast and securely
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

{{--            <div class="bg-b-csm p-0" style="opacity: 0.1; filter: blur(100px);">;--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">--}}
{{--                    <path fill="var(--bs-primary)" fill-opacity="1"--}}
{{--                          d="M0,256L48,234.7C96,213,192,171,288,138.7C384,107,480,85,576,96C672,107,768,149,864,176C960,203,1056,213,1152,181.3C1248,149,1344,75,1392,37.3L1440,0L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>--}}
{{--                </svg>--}}
{{--            </div>--}}

            {{--  Partners  --}}
            <div class="row justify-content-center" style="margin-top: 6rem;" wire:ignore>
                <h2 class="text-center text-primary fw-bolder">Providers</h2>
                <p class="text-center">We work with the best providers to ensure you have the best experience</p>

                <div class="w-100" style="background: var(--bs-base); border-radius: 20px">

                    <!-- Bootstrap Carousel -->
                    <div class="partner-slider p-5">
                        <div class="partner-track">
                            @foreach(\App\Models\Provider::active()->get() as $provider)
                                <div class="partner-logo">
                                    <img src="{{ \Storage::url($provider->image) }}" alt="{{ $provider->name }}">
                                </div>
                            @endforeach

                            <!-- Duplicate logos for smooth looping -->
                            @foreach(\App\Models\Provider::active()->get() as $provider)
                                <div class="partner-logo">
                                    <img src="{{ \Storage::url($provider->image) }}" alt="{{ $provider->name }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{--                    <div class="swiper partners-swiper">--}}
                    {{--                        <div class="swiper-wrapper">--}}
                    {{--                            @foreach(\App\Models\Provider::active()->get() as $provider)--}}
                    {{--                                <div class="swiper-slide">--}}
                    {{--                                    <div class="d-flex justify-content-center align-items-center me-4">--}}
                    {{--                                        <img src="{{ \Storage::url($provider->image) }}" alt="{{ $provider->name }}"--}}
                    {{--                                             class="img-fluid opacity-75" style="max-height: 80px;">--}}
                    {{--                                    </div>--}}
                    {{--                                </div>--}}
                    {{--                            @endforeach--}}
                    {{--                            <!-- Add more partners following the same pattern -->--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                </div>
            </div>


{{--            <div class="bg-b-csm p-0" style="opacity: 0.1; filter: blur(100px);">;--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">--}}
{{--                    <path fill="var(--bs-primary)" fill-opacity="1"--}}
{{--                          d="M0,256L48,234.7C96,213,192,171,288,138.7C384,107,480,85,576,96C672,107,768,149,864,176C960,203,1056,213,1152,181.3C1248,149,1344,75,1392,37.3L1440,0L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>--}}
{{--                </svg>--}}
{{--            </div>--}}


            {{--  FAQ  --}}
            <div class="row" style="margin-top: 6rem;">
                <h2 class="text-center text-primary fw-bolder">Your  {{ config('app.name') }} questions answered</h2>
                <p class="text-center">Here are some frequently asked questions to help you if you are unsure about something, or if you have any questions about how to earn money on {{ config('app.name') }}.</p>

                <!-- Nav tabs -->
                <ul class="nav nav-pills" id="faqTab" role="tablist">
                    @foreach($faqsInfo as $category => $faqs)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center {{ $loop->first ? 'active' : '' }}"
                                    style="border-radius: 20px;"
                                    id="{{ $category }}-tab"
                                    data-bs-toggle="tab" href="#{{ $category }}" role="tab"
                                    aria-controls="{{ $category }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                {{ ucfirst($category) }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <!-- Tab content -->
                <div class="tab-content p-0 mt-3 position-relative" id="faqTabContent">
                    @foreach($faqsInfo as $category => $faqs)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $category }}"
                             role="tabpanel" aria-labelledby="{{ $category }}-tab">
                            <div class="accordion">
                                @foreach($faqs as $index => $faq)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button f-md text-white" type="button"
                                                    style="border-radius: 20px;"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#as-{{ $index }}">
                                                {{ $faq['question'] }}
                                            </button>
                                        </h2>
                                        <div id="as-{{ $index }}"
                                             class="accordion-collapse collapse {{ $loop->iteration == 1 ? 'show' : '' }}">
                                            <div class="accordion-body small">
                                                {{ $faq['answer'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>

{{--            <div class="bg-b-csm p-0" style="opacity: 0.1; filter: blur(100px);">;--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">--}}
{{--                    <path fill="var(--bs-primary)" fill-opacity="1"--}}
{{--                          d="M0,256L48,234.7C96,213,192,171,288,138.7C384,107,480,85,576,96C672,107,768,149,864,176C960,203,1056,213,1152,181.3C1248,149,1344,75,1392,37.3L1440,0L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path>--}}
{{--                </svg>--}}
{{--            </div>--}}


            {{--  Start Earning  --}}
            <div class="row justify-content-center " style="margin-top: 6rem;" wire:ignore>
                <div class="w-100 "
                     style="background: var(--bs-base); border-radius: 20px;">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                        <!-- Left Column -->
                        <div class="d-flex flex-column p-3 py-5 p-md-5 col-md-6 col-12">
                            <div class="d-flex flex-column w-100 gap-4">
                                <!-- Heading -->
                                <div class="d-flex flex-wrap gap-3 text-white">
                                    <div>
                                        <span class="text-primary fw-bolder fs-1">Start earning with {{ config('app.name') }} today</span>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="text-secondary fs-5 fw-light">
                                    <p>Become a website and game tester, share your opinions in surveys, and earn cashback on your online shopping. Join now and start making money easily!</p>
                                </div>
                            </div>

                            <!-- Button -->
                            <button class="btn btn-primary d-flex align-items-center w-100 p-3 text-uppercase"
                                    style="border-radius: 25px;"
                                    data-bs-target="#authModal" data-bs-toggle="modal">
                                <span>Sign Up</span>
                            </button>
                        </div>

                        <!-- Right Column (Image) -->
                        <div class="overflow-hidden ">
{{--                                                        <x-svg.wallet width="300px" height="300px"/>--}}

{{--                            <link--}}
{{--                                rel="stylesheet"--}}
{{--                                href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"--}}
{{--                            />--}}
                            <img loading="lazy"
                                 src="{{ asset('assets/img/games.avif') }}"
                                 alt="Start Earning"
                                 style="max-height: 300px;">
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

