<div>
    <div
        wire:ignore.self
        class="modal fade"
        id="authModal"
        tabindex="-1"
        aria-labelledby="offerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content auth-width"
                 x-data="{ activeTab: @entangle('activeTab') }">
                <div class="modal-header">
                    <h5 class="modal-title text-body" x-text="activeTab === 1 ? 'Log In' : 'Join Now'"></h5>
                    <button type="button"
                            class="btn-close bg-label-primary d-flex align-items-center justify-content-center"
                            style="background: var(--bs-base); border: none; color: white"
                            data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-times" style="font-weight: 600"></i>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div class="auth-tabs-container rounded position-relative  auth-width">
                        <ul class="nav nav-pills m-auto mb-0 rounded" role="tablist"
                            id="auth-tabs">
                            <li class="nav-item w-50" role="presentation">
                                <button type="button"
                                        @class(['nav-link d-flex align-items-center ', 'active' => $activeTab === 1])
                                        wire:click="$set('activeTab', 1)"
                                        role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tab-login">
                                    Log in
                                </button>
                            </li>
                            <li class="nav-item w-50" role="presentation">
                                <button type="button"
                                        @class(['nav-link d-flex align-items-center', 'active' => $activeTab === 2])
                                        wire:click="$set('activeTab', 2)"
                                        role="tab"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tab-register">
                                    Join Now
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content pt-1 " style="background-color: var(--bs-base) !important;">
                        <div @class(['tab-pane fade', 'active show' => $activeTab === 1]) id="tab-login"
                             role="tabpanel">
                            <div class="d-flex col-12 align-items-center py-4">
                                <div class="w-px-400 mx-auto">
                                    <div class="app-brand mb-2">
                                        <x-logo/>
                                    </div>

                                    <p class="mb-4">Please log-in to your account and start the adventure 🚀</p>
                                    <form class="mb-3" wire:submit="login" x-data="{ showPassword: false }">
                                        <div class="mb-3">
                                            <label class="form-label text-body">Email</label>
                                            <input @class(['form-control', 'is-invalid' => $errors->has('email')])
                                                   wire:model="email"
                                                   type="text"
                                                   name="email"
                                                   placeholder="Enter your email"
                                                   autofocus=""
                                                   autocomplete="email">

                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 form-password-toggle">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label text-body">Password</label>
                                                <a href="javascript:"
                                                   data-bs-target="#forgetPasswordModal"
                                                   data-bs-toggle="modal">
                                                    <small>Forgot Password?</small>
                                                </a>
                                            </div>
                                            <div class="input-group input-group-merge has-validation">
                                                <input
                                                    @class(['form-control', 'is-invalid' => $errors->has('password')])
                                                    wire:model="password"
                                                    x-bind:type="showPassword ? 'text' : 'password'"
                                                    type="password"
                                                    name="password"
                                                    placeholder="············"
                                                    aria-describedby="password"
                                                    autocomplete="current-password"
                                                >
                                                <span class="input-group-text cursor-pointer"
                                                      x-on:click="showPassword = !showPassword">
                                                    <x-heroicon-o-eye-slash x-show="!showPassword" class="text-body"
                                                                            width="20px"/>
                                                    <x-heroicon-o-eye x-show="showPassword" class="text-body"
                                                                      width="20px"/>
                                                </span>

                                                @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                   <div class="mb-3">
                             <label>
                                    <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>

                                        <span class="text-danger" id="captchaError"></span>
                                   </label>
                                   
                                   </div>
                                        
                                        <div class="mb-3">
                                            <div class="form-check form-label text-body">
                                                <input class="form-check-input" type="checkbox" name="remember">
                                                <label class="form-check-label">Remember Me</label>
                                            </div>
                                        </div>
                                        <button
                                            class="btn btn-primary d-flex align-items-center w-100 waves-effect waves-light"
                                            type="submit">

                                            <span wire:loading wire:target="login"
                                                  class="spinner-border spinner-border-sm me-2" role="status"
                                                  aria-hidden="true"></span>
                                            Login
                                        </button>

                                        {{--                                        <button--}}
                                        {{--                                            class="mt-2 btn btn-danger d-flex align-items-center w-100 waves-effect waves-light"--}}
                                        {{--                                            type="button"--}}
                                        {{--                                            wire:loading.attr="disabled"--}}
                                        {{--                                            wire:click="loginAsAdmin">--}}
                                        {{--                                                      <span wire:loading wire:target="loginAsAdmin"--}}
                                        {{--                                                            class="spinner-border spinner-border-sm me-2" role="status"--}}
                                        {{--                                                            aria-hidden="true"></span>--}}
                                        {{--                                            Login as admin--}}
                                        {{--                                        </button>--}}
                                    </form>

                                    <p class="text-center form-label">
                                        <span class="text-body">New on our platform?</span>
                                        <a href="javascript: document.querySelector(`button[data-bs-target='#tab-register']`).click()">
                                            <span>Create an account</span>
                                        </a>
                                    </p>

                                    <div class="divider my-2">
                                        <div class="divider-text">or</div>
                                    </div>

                                    <a
                                        href="{{ route('auth.google') }}"
                                        class="btn btn-dark d-flex align-items-center w-100 waves-effect waves-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                             viewBox="0 0 28 28"
                                             fill="none">
                                            <g clip-path="url(#clip0_436_15312)">
                                                <path
                                                    d="M6.20539 16.9208L5.23075 20.5592L1.66846 20.6346C0.603859 18.66 0 16.4008 0 14C0 11.6785 0.564594 9.48921 1.56537 7.56153L4.73758 8.14297L6.12686 11.2954C5.83609 12.1431 5.6776 13.0531 5.6776 14C5.67771 15.0277 5.86387 16.0123 6.20539 16.9208Z"
                                                    fill="#FFC700"/>
                                                <path
                                                    d="M27.7554 11.3846C27.9162 12.2315 28 13.1061 28 14C28 15.0023 27.8946 15.98 27.6939 16.9231C27.0123 20.1323 25.2316 22.9346 22.7647 24.9177L22.7639 24.9169L18.7693 24.7131L18.2039 21.1839C19.8408 20.2239 21.1201 18.7216 21.794 16.9231H14.3078V11.3846H27.7554Z"
                                                    fill="#518EF8"/>
                                                <path
                                                    d="M22.7639 24.9169L22.7647 24.9177C20.3655 26.8461 17.3177 28 14 28C8.66846 28 4.03309 25.02 1.66846 20.6346L6.20539 16.9208C7.38768 20.0761 10.4315 22.3223 14 22.3223C15.5338 22.3223 16.9709 21.9077 18.2039 21.1839L22.7639 24.9169Z"
                                                    fill="#DD2E18"/>
                                                <path
                                                    d="M22.9362 3.22306L18.4008 6.93613C17.1246 6.13845 15.6161 5.67766 14 5.67766C10.3508 5.67766 7.24992 8.02687 6.12686 11.2954L1.56537 7.56153C3.89539 3.06923 8.58922 0 14 0C17.3969 0 20.5115 1.21002 22.9362 3.22306Z"
                                                    fill="#C34646"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_436_15312">
                                                    <rect width="28" height="28" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <span class="ms-2">Log In Via Google</span>
                                    </a>

                                </div>
                            </div>
                        </div>
                        <div @class(['tab-pane fade', 'active show' => $activeTab === 2]) id="tab-register"
                             role="tabpanel">
                            <div class="d-flex col-12  align-items-center py-4">
                                <div class="w-px-400 mx-auto">
                                    <div class="app-brand mb-2">
                                        <x-logo/>
                                    </div>

                                    <p class="mb-4">It's free! Join Now and start to earn money! 🚀</p>
                                    <form class="mb-3" wire:submit="register">
                                        <div class="mb-3">
                                            <label class="form-label text-body">Email</label>
                                            <input @class(['form-control', 'is-invalid' => $errors->has('email')])
                                                   wire:model="email"
                                                   type="text"
                                                   name="email"
                                                   placeholder="Enter your email or username"
                                                   autofocus=""
                                                   autocomplete="email">

                                            @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3 form-password-toggle" x-data="{ showPassword: false }">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label text-body">Password</label>
                                            </div>
                                            <div class="input-group input-group-merge has-validation">
                                                <input
                                                    @class(['form-control', 'is-invalid' => $errors->has('password')])
                                                    wire:model="password"
                                                    x-bind:type="showPassword ? 'text' : 'password'"
                                                    type="password"
                                                    name="password"
                                                    placeholder="············"
                                                    aria-describedby="password"
                                                    autocomplete="current-password">
                                                <span class="input-group-text cursor-pointer"
                                                      x-on:click="showPassword = !showPassword">
                                                    <x-heroicon-o-eye-slash x-show="!showPassword" width="20px"/>
                                                    <x-heroicon-o-eye x-show="showPassword" width="20px"/>
                                                </span>

                                                @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                         <div class="mb-3">
                                        
                                        <div class="mb-3">
                             <label>
                                    <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                                        <span class="text-danger" id="captchaError"></span>
                                   </label>
                                   
                                   </div>
                                   
                                   </div>
                                        <div class="mb-3">
                                            <div class="form-check form-label text-body">
                                                <input
                                                    @class(['form-check-input', 'is-invalid' => $errors->has('acceptTerms')])
                                                    type="checkbox"
                                                    name="terms-conditions"
                                                    wire:model="acceptTerms">
                                                <label class="form-check-label">
                                                    I agree to the <a href="{{ route('terms') }}">terms and
                                                        conditions</a>
                                                </label>

                                                @error('acceptTerms')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <button
                                            class="btn btn-primary d-flex align-items-center w-100 waves-effect waves-light">
                                            <span wire:loading wire:target="register"
                                                  class="spinner-border spinner-border-sm me-2" role="status"
                                                  aria-hidden="true"></span>
                                            Register
                                        </button>
                                    </form>

                                    <p class="text-center form-label">
                                        <span class="text-body">Already have an account?</span>
                                        <a href="javascript: document.querySelector(`button[data-bs-target='#tab-login']`).click()">
                                            <span>Sign in</span>
                                        </a>
                                    </p>
                                    <div class="divider my-2">
                                        <div class="divider-text">or</div>
                                    </div>
                                    <a href="{{ route('auth.google') }}"
                                       class="btn btn-dark d-flex align-items-center w-100 waves-effect waves-light">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                             viewBox="0 0 28 28"
                                             fill="none">
                                            <g clip-path="url(#clip0_436_15312)">
                                                <path
                                                    d="M6.20539 16.9208L5.23075 20.5592L1.66846 20.6346C0.603859 18.66 0 16.4008 0 14C0 11.6785 0.564594 9.48921 1.56537 7.56153L4.73758 8.14297L6.12686 11.2954C5.83609 12.1431 5.6776 13.0531 5.6776 14C5.67771 15.0277 5.86387 16.0123 6.20539 16.9208Z"
                                                    fill="#FFC700"/>
                                                <path
                                                    d="M27.7554 11.3846C27.9162 12.2315 28 13.1061 28 14C28 15.0023 27.8946 15.98 27.6939 16.9231C27.0123 20.1323 25.2316 22.9346 22.7647 24.9177L22.7639 24.9169L18.7693 24.7131L18.2039 21.1839C19.8408 20.2239 21.1201 18.7216 21.794 16.9231H14.3078V11.3846H27.7554Z"
                                                    fill="#518EF8"/>
                                                <path
                                                    d="M22.7639 24.9169L22.7647 24.9177C20.3655 26.8461 17.3177 28 14 28C8.66846 28 4.03309 25.02 1.66846 20.6346L6.20539 16.9208C7.38768 20.0761 10.4315 22.3223 14 22.3223C15.5338 22.3223 16.9709 21.9077 18.2039 21.1839L22.7639 24.9169Z"
                                                    fill="#DD2E18"/>
                                                <path
                                                    d="M22.9362 3.22306L18.4008 6.93613C17.1246 6.13845 15.6161 5.67766 14 5.67766C10.3508 5.67766 7.24992 8.02687 6.12686 11.2954L1.56537 7.56153C3.89539 3.06923 8.58922 0 14 0C17.3969 0 20.5115 1.21002 22.9362 3.22306Z"
                                                    fill="#C34646"/>
                                            </g>
                                            <defs>
                                                <clipPath id="clip0_436_15312">
                                                    <rect width="28" height="28" fill="white"/>
                                                </clipPath>
                                            </defs>
                                        </svg>

                                        <span class="ms-2">Join In via Google</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div
        wire:ignore.self
        class="modal fade"
        id="forgetPasswordModal"
        tabindex="-1"
        aria-labelledby="forgetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header mb-0 pb-0">
                    <h5 class="modal-title f-md text-body" id="forgetPasswordModalLabel">Send Reset Mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="forgetPassword">
                        <div class="mb-3">
                            <label class="form-label text-body">Email</label>
                            <input type="email"
                                   name="email"
                                   autocomplete="email"
                                   wire:model="email"
                                   @class(['form-control', 'is-invalid' => $errors->has('email')])
                                   placeholder="Enter your email">

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit"
                                class="btn btn-primary d-flex align-items-center w-100 waves-effect waves-light">
                            Continue
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div
        wire:ignore.self
        x-data="{ show: {{ $this->isHasValidToken() ? 'true' : 'false' }} }"
        x-init="if (show) { $('#resetPasswordModal').modal('show') }"
        class="modal fade"
        id="resetPasswordModal"
        tabindex="-1"
        aria-labelledby="resetPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header mb-0 pb-0">
                    <h5 class="modal-title f-md text-body" id="resetPasswordModalLabel">Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form wire:submit="resetPassword">
                        <div class="mb-3">
                            <label class="form-label text-body">Token</label>
                            <input type="text"
                                   wire:model="token"
                                   @class(['form-control', 'is-invalid' => $errors->has('token')])
                                   disabled>

                            @error('token')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-body">Email</label>
                            <input type="text"
                                   name="email"
                                   autocomplete="email"
                                   wire:model="email"
                                   @class(['form-control', 'is-invalid' => $errors->has('email')])
                                   disabled>

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle" x-data="{ showPassword: false }">
                            <div class="d-flex justify-content-between">
                                <label class="form-label text-body">New Password</label>
                            </div>
                            <div class="input-group input-group-merge has-validation">
                                <input
                                    @class(['form-control', 'is-invalid' => $errors->has('password')])
                                    wire:model="password"
                                    x-bind:type="showPassword ? 'text' : 'password'"
                                    type="password"
                                    name="password"
                                    placeholder="············"
                                    aria-describedby="password"
                                    autocomplete="current-password">
                                <span class="input-group-text cursor-pointer" x-on:click="showPassword = !showPassword">
                                    <x-heroicon-o-eye-slash x-show="!showPassword" width="20px"/>
                                    <x-heroicon-o-eye x-show="showPassword" width="20px"/>
                                </span>

                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit"
                                    class="btn btn-primary d-flex align-items-center w-50 waves-effect waves-light">
                                Continue
                            </button>

                            <a href="{{ route('home') }}"
                               class="btn btn-danger d-flex align-items-center w-50 waves-effect waves-light">
                                Close
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    document.getElementById('authModal').addEventListener('show.bs.modal', function () {
        const button = event.relatedTarget
        if (button.getAttribute('data-signup') === 'true')
            document.querySelector(`button[data-bs-target='#tab-register']`).click();
        else
            document.querySelector(`button[data-bs-target='#tab-login']`).click();
    });
</script>




<script>
    function renderRecaptcha() {
        const el = document.getElementById('recaptcha');
        if (typeof grecaptcha !== 'undefined' && el && el.offsetParent !== null) {
            grecaptcha.render(el, {
                sitekey: '{{ config('services.recaptcha.site_key') }}'
            });
        }
    }

    document.addEventListener('DOMContentLoaded', renderRecaptcha);

    window.addEventListener('recaptcha:refresh', () => {
        renderRecaptcha();
    });
</script>

<script src="https://www.google.com/recaptcha/api.js?onload=renderRecaptcha&render=explicit" async defer></script>

@endscript
