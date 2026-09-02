<div>
    <div class="card">
        <div class="card-header pb-0">
            <h5 class="card-title fw-bold text-white">
                <i class="fa-solid fa-handshake-angle text-primary"></i> Referrals
            </h5>
        </div>

        <div class="card-body mt-3 px-3">
            <div class="row">
                <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                    <div class="card h-100 bg-body ">
                        <div class="card-body text-center">
                            <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                                <i class="fa-solid fa-users" style="font-size: 20px"></i>
                            </div>
                            <h5 class="card-title mb-2 text-white f-md">{{ auth()->user()?->referrals()?->count() ?? 0 }}</h5>
                            <small class="text-body h6">Users Referred</small>
                        </div>
                    </div>
                </div>

                <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                    <div class="card h-100 bg-body">
                        <div class="card-body text-center">
                            <div class="badge rounded-pill p-2 bg-label-warning mb-2">
                                <i class="fa-solid fa-hand-holding-dollar" style="font-size: 20px"></i>
                            </div>
                            @php
                                $referralPoints = auth()->user()?->referralData()->first()->referral_points ?? 0;
                            @endphp
                            <div class="d-flex align-items-center justify-content-center mb-2">
{{--                                <i class="fa-solid fa-coins text-white me-2" style="font-size: 14px" x-show="is_coin == '1'"></i>--}}
                                <img src="{{ asset('assets/img/coin.png') }}" alt="" width="12px" class="me-1" x-show="is_coin == '1'">

                                <h5 class="card-title  mb-0 text-white f-md"
                                    x-text="is_coin == '0' ? '${{ to_money_str($referralPoints) }}' : '{{ $referralPoints }}'">
                                </h5>
                            </div>

                            <small class="text-body h6">Referral Earnings</small>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card bg-body">
                <div class="card-header pb-0">
                    <h5 class="card-title fw-bold text-white">
                        Refer a Friend
                    </h5>

                    <span class="small text secondary fw-normal">
                        <span>Get 5% of their earnings for life!</span>
                    </span>

                    <p class="card-text small">
                        Referred friends will also receive a bonus of <span
                            class="fw-bolder text-primary">{{ setting('referral.points', 100) }} coins</span> when
                        they sign up.
                    </p>
                </div>

                <div class="card-body mt-3">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control bg-card border-dark text-white " style="font-size: 14px"
                               value="{{ route('referral', auth()->user()->referralData()->first()->referral_code) }}"
                               readonly>
                        <button class="btn btn-dark  f-md" type="button" onclick="copyReferralLink()">Copy</button>
                    </div>

                    <div class="alert alert-success d-none mt-3 small" id="copyAlert">Referral link copied to
                        clipboard!
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>

{{--@script--}}
<script>
    function copyReferralLink() {
        const copyText = document.querySelector('.input-group input');
        copyText.select();
        document.execCommand('copy');

        // Show alert
        const copyAlert = document.getElementById('copyAlert');
        copyAlert.classList.remove('d-none');
        setTimeout(() => {
            copyAlert.classList.add('d-none');
        }, 2000);
    }
</script>
{{--@endscript--}}
