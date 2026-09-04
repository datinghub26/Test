<footer class="content-footer footer small mt-4" style="background: #18181b;">
    <div class="container-fluid">
        <div class="pt-5 pb-4 py-md-5" style="background: #18181b;">
            <div class="row">
                <div class="col-md-4 text-center text-md-start mt-4 mt-md-0 order-last order-md-first">
                    <x-logo/>
                    <p class="footer-text small mt-2 mb-0">All rights reserved by ERC &copy; {{ now()->year }}</p>
{{--            

{{--                    <a href="//www.dmca.com/Protection/Status.aspx?ID=9fb78912-8093-4c25-85f1-98bd3dcb7c13"--}}
{{--                       title="DMCA.com Protection Status" class="dmca-badge"> <img--}}
{{--                            src="https://images.dmca.com/Badges/dmca-badge-w100-5x1-03.png?ID=9fb78912-8093-4c25-85f1-98bd3dcb7c13"--}}
{{--                            alt="DMCA.com Protection Status"/></a>--}}
{{--                    <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script>--}}
                </div>

                <div class="col-md-8 mb-4">
                    <div
                        class="d-flex flex-wrap justify-content-md-around justify-content-sm-between justify-content-around row-gap-4">
                        <div class=" text-md-start">
                            <h5 class="fw-bold text-white">About</h5>
                            <div class="d-flex flex-column justify-content-around gap-2">
                                <a href="{{ route('terms') }}" class="footer-link">
                                    <x-heroicon-s-document-text class="me-1" width="18px" height="18px"/>
                                    Terms of Service
                                </a>

                                <a href="{{ route('privacy') }}" class="footer-link">
                                    <x-heroicon-s-shield-check class="me-1" width="18px" height="18px"/>
                                    Privacy Policy
                                </a>
                            </div>
                        </div>

                        <div class=" text-md-start">
                            <h5 class="fw-bold text-white">Support</h5>
                            <div class="d-flex flex-column justify-content-around gap-2">
                                <a href="mailto:{{ config('mail.from.address') }}" class="footer-link">
                                    <x-heroicon-s-envelope class="me-1" width="18px" height="18px"/>
                                    Contact Us
                                </a>

                                <a href="{{ route('faq') }}" class="footer-link">
                                    <x-heroicon-s-question-mark-circle class="me-1" width="18px" height="18px"/>
                                    FAQ
                                </a>
                            </div>
                        </div>

                        <div class="text-center text-md-start">
                            <h5 class="fw-bold text-white">Social media</h5>
                            <div class="d-flex flex-wrap justify-content-around gap-1">
                                @if(setting('social.linkedin'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn" target="_blank"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="LinkedIn"
                                       href="{{ setting('social.linkedin') }}">
                                        <i class="fa-brands fa-linkedin"></i>
                                    </a>
                                @endif

                                @if(setting('social.facebook'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn" target="_blank"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Facebook"
                                       href="{{ setting('social.facebook') }}">
                                        <i class="fa-brands fa-facebook"></i>
                                    </a>
                                @endif

                                @if(setting('social.telegram'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Telegram"
                                       href="{{ setting('social.telegram') }}" target="_blank">
                                        <i class="fa-brands fa-telegram"></i>
                                    </a>
                                @endif

                                @if(setting('social.twitter'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Twitter"
                                       href="" target="_blank">
                                        <i class="fa-brands fa-twitter"></i>
                                    </a>
                                @endif

                                @if(setting('social.instagram'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Instagram"
                                       href="" target="_blank">
                                        <i class="fa-brands fa-instagram"></i>
                                    </a>
                                @endif

                                @if(setting('social.trustpilot'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Trustpilot"
                                       href="" target="_blank">
                                        <i class="fa-solid fa-star"></i>
                                    </a>
                                @endif

                                @if(setting('social.reddit'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Reddit"
                                       href="{{ setting('social.reddit') }}" target="_blank">
                                        <i class="fa-brands fa-reddit"></i>
                                    </a>
                                @endif

                                @if(setting('social.discord'))
                                    <a class="btn btn-sm btn-label-secondary footer-link-btn"
                                       data-bs-toggle="tooltip" data-bs-placement="top" title="Discord"
                                       href="{{ setting('social.discord') }}" target="_blank">
                                        <i class="fa-brands fa-discord"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<div class="mobile-navbar d-xl-none">
    <a href="{{ route('leaderboard') }}" @class(['nv-item', 'active' => request()->routeIs('leaderboard')])>
         <span>
             <x-heroicon-s-arrow-trending-up style="width: 24px; height: 24px"/>
             <span>Ranking</span>
         </span>
    </a>
    <a href="{{ route('shop') }}" @class(['nv-item', 'active' => request()->routeIs('shop')])>
        <span>
            <x-heroicon-s-wallet style="width: 24px; height: 24px"/>
            <span>Cashout</span>
        </span>
    </a>
    <a href="{{ route('earn') }}" @class(['nv-item', 'active' => request()->routeIs('earn')])>
         <span>
            <x-heroicon-s-home style="width: 24px; height: 24px"/>
            <span>Home</span>
         </span>
    </a>

    <a href="{{ route('rewards') }}" @class(['nv-item', 'active' => request()->routeIs('rewards')])>
        <span>
            <x-heroicon-s-gift style="width: 24px; height: 24px"/>
            <span>Rewards</span>
        </span>
    </a>

    <a @click="$dispatch('open-chat')" class="nv-item">
       <span>
           <x-heroicon-s-chat-bubble-bottom-center-text style="width: 24px; height: 24px"/>
           <span>Chat</span>
       </span>
    </a>
</div>
