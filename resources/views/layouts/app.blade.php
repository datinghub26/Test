<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    class="dark-style layout-navbar-fixed layout-menu-fixed"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="{{ asset('assets') }}/"
    data-template="horizontal-menu-template"
>
    <head>
        <meta charset="utf-8"/>
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
        />

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title ?? config('app.name')  . ' | Earn. Reward. Repeat' }}</title>
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/icon-light.png') }}?v=2"/>

        <!-- SEO -->
        @include('layouts.sections.seo')

        <!-- Icons -->
        @vite([
                "resources/assets/vendor/fonts/fontawesome.scss",
                "resources/assets/vendor/fonts/flag-icons.scss",
                ])

        <!-- Core CSS -->
        @vite([
              "resources/assets/vendor/scss/rtl/core-dark.scss",
              "resources/assets/vendor/scss/rtl/theme-default-dark.scss",
              "resources/assets/css/demo.css",
              ])

        @vite([
               "resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss",
               "resources/assets/vendor/libs/typeahead-js/typeahead.scss"
               ])

        @vite(['resources/assets/vendor/js/helpers.js', 'resources/assets/js/config.js'])
        @vite(['resources/assets/css/app.css'])
        @stack('css')

        <!-- Google tag (gtag.js) -->
        @if(setting('services.google.analytics_enable'))
            <script async defer
                    src="https://www.googletagmanager.com/gtag/js?id={{ setting('services.google.analytics_id') }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];

                function gtag() {
                    dataLayer.push(arguments);
                }

                gtag('js', new Date());
                gtag('config', '{{ setting('services.google.analytics_id') }}');
            </script>
        @endif
        
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

 
    </head>

    <body>
        <div id="preloader">
            <div class="text-center">
                <x-logo class="mx-auto"/>
                <div class="loader m-auto"></div>
            </div>
        </div>


        <!-- Layout wrapper -->
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                @include('layouts.sections.sidebar')

                <!-- Layout container -->
                <div class="layout-page"
                     x-data="{ is_coin: (typeof localStorage !== 'undefined' ? localStorage.getItem('isCoin') || '0' : '0') }"
                     x-on:update-coins.window="is_coin = $event.detail.isCoin">

                    @include('layouts.sections.navbar')
                    <livewire:user.live-cashouts/>

                    <!-- Content wrapper -->
                    <div class="content-wrapper">
                        <!-- Content -->
                        <div class="container-fluid flex-grow-1 container-p-y">
                            {{ $slot }}
                        </div>
                        <!-- / Content -->

                        @include('layouts.sections.footer')
                        <div class="content-backdrop fade"></div>
                    </div>
                    <!-- Content wrapper -->

                </div>
                <!-- / Layout page -->
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>

            <!-- Drag Target Area To SlideIn Menu On Small Screens -->
            <div class="drag-target"></div>

            @auth
                @if(!auth()->user()->isCompletedProfile())
                    <livewire:user.complete-profile-modal/>
                @endif

                <livewire:user.block-modal/>
                <livewire:user.bonus-modal/>
            @else
                <livewire:user.auth/>
            @endauth

            <livewire:live-chat/>
            <livewire:user.activity-modal/>
        </div>

        <x-toaster-hub/> <!-- 👈 -->
        @stack('modals')
        @include('layouts.modals.offers-api')

        @vite([
                'resources/assets/vendor/libs/jquery/jquery.js',
                'resources/assets/vendor/js/bootstrap.js',
                'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
                'resources/assets/vendor/libs/typeahead-js/typeahead.js',
                'resources/assets/vendor/js/menu.js'
                ])

        @vite(['resources/assets/js/main.js'])
        @vite(['resources/assets/js/app.js'])

        @stack('js')

        <!-- Audio notification for offers, cashouts, and balance updates -->
        @php
            $customSound = setting('general.notification_sound');
            if (is_array($customSound)) {
                $customSound = reset($customSound);
            }
            $soundSrc = null;
            if ($customSound) {
                if (str_starts_with($customSound, 'http://') || str_starts_with($customSound, 'https://')) {
                    $soundSrc = $customSound;
                } else {
                    $soundSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($customSound);
                }
            }
            if (!$soundSrc) {
                $soundSrc = file_exists(public_path('assets/sounds/notification.mp3')) 
                    ? asset('assets/sounds/notification.mp3') 
                    : asset('assets/sounds/coin-withdraw.mp3');
            }
        @endphp
        <audio id="globalCashoutAudio" src="{{ $soundSrc }}" preload="auto"></audio>
        <script>
            (function () {
                let userInteracted = false;
                function unlockAudio() {
                    if (userInteracted) return;
                    const audio = document.getElementById('globalCashoutAudio');
                    if (audio) {
                        const originalMuted = audio.muted;
                        audio.muted = true;
                        audio.play().then(() => {
                            audio.pause();
                            audio.currentTime = 0;
                            audio.muted = originalMuted;
                            userInteracted = true;
                            window.removeEventListener('click', unlockAudio);
                            window.removeEventListener('keydown', unlockAudio);
                        }).catch(() => {
                            audio.muted = originalMuted;
                        });
                    }
                }
                window.addEventListener('click', unlockAudio, { once: false });
                window.addEventListener('keydown', unlockAudio, { once: false });

                window.playUserCashoutSound = function (eventId) {
                    try {
                        if (!eventId) return;

                        const key = 'played_notif_sound_events';
                        let played = [];
                        try {
                            played = JSON.parse(localStorage.getItem(key) || '[]');
                        } catch (e) { played = []; }

                        if (played.includes(eventId)) return;

                        const audio = document.getElementById('globalCashoutAudio');
                        if (audio) {
                            audio.currentTime = 0;
                            audio.volume = 0.7;
                            const p = audio.play();
                            if (p !== undefined) {
                                p.then(() => {
                                    played.push(eventId);
                                    if (played.length > 100) played.shift();
                                    localStorage.setItem(key, JSON.stringify(played));
                                }).catch(() => {});
                            }
                        }
                    } catch (e) {}
                };

                function resolveEventId(e, defaultPrefix) {
                    if (!e) return null;
                    let val = null;
                    if (typeof e.detail === 'object' && e.detail !== null) {
                        val = e.detail.id || (Array.isArray(e.detail) ? e.detail[0]?.id : null);
                    } else if (typeof e.detail === 'string' || typeof e.detail === 'number') {
                        val = e.detail;
                    }
                    return val ? (defaultPrefix + '_' + val) : null;
                }

                window.addEventListener('play-notification-sound', function (e) {
                    const id = resolveEventId(e, 'notif');
                    if (id) window.playUserCashoutSound(id);
                });
            })();
        </script>
    </body>
</html>
