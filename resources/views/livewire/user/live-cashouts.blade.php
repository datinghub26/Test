@assets
<style>
    /* Live background pulse */
    .live-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        animation: pulse 2s infinite ease-in-out;
    }

    /* Animation for the signal icon */
    .live-icon {
        animation: pulse 1.5s infinite ease-in-out;
        color: var(--bs-primary) !important;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 0.8;
        }
        50% {
            transform: scale(1.18);
            opacity: 1;
        }
        100% {
            transform: scale(1);
            opacity: 0.8;
        }
    }

    .live-cashout-badge {
        background: rgba(55, 231, 128, 0.12) !important;
        border: 1px solid rgba(55, 231, 128, 0.3) !important;
        color: #37E780 !important;
    }
</style>
@endassets

<div wire:poll.20s>
    <div class="container-fluid" x-data="{ lastCount: {{ count($cashouts) }} }">
        <div class="d-flex justify-content-start mt-2 small align-items-center">
            <div class="card text-white me-2 py-2 px-3 border-0 shadow-sm" style="background: var(--bs-card-bg);">
                <div class="card-body p-0 pb-0 text-center">
                    <div class="d-flex justify-content-center gap-2">
                        <div class="align-items-center position-relative">
                            <div class="live-bg"></div>
                            <x-heroicon-s-rocket-launch class="text-primary live-icon" width="25px"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper"
                 x-init="new window.swiper($el, { slidesPerView: 'auto', autoplay: { delay: 5000, disableOnInteraction: false } })">
                <div class="swiper-wrapper" wire:ignore.self>
                    @foreach($cashouts as $withdrawal)
                        <div class="swiper-slide fade-in-scale card text-white me-2 p-2 border-0"
                             style="cursor: pointer; width: auto !important; background: var(--bs-card-bg);"
                             tooltip="true"
                             data-bs-html="true"
                             data-bs-placement="bottom"
                             title="<div class='text-start text-body'><p class='m-0'>Username: {{ $withdrawal->user->username ?? 'Anonymous' }}</p> <p class='m-0'>Name: {{ $withdrawal->name }}</p> <p class='m-0'>Amount: {{ $withdrawal->amount }} ERC</p> </div>">
                            <div class="card-body p-0 pb-0 text-center"
                                 @click="$dispatch('activity-open', {user_id: '{{ $withdrawal->user_id }}'})">
                                <div class="d-flex justify-content-center align-items-center gap-2">
                                    <div
                                        class="d-flex justify-content-center rounded-circle align-items-center bg-secondary bg-opacity-25 text-white overflow-hidden"
                                        style="width: 30px; height: 30px; background-color: {{ $withdrawal->bg_color ? $withdrawal->bg_color . ' !important' : '' }}">
                                        <img
                                            height="100%"
                                            width="100%"
                                            @class(['object-fit-contain', 'p-1' => $withdrawal instanceof \App\Models\CashoutRequest])
                                            src="{{ $withdrawal->method_image ? \Storage::url($withdrawal->method_image) : ($withdrawal->user ? $withdrawal->user->avatar() : asset('assets/img/icon-light.png')) }}"
                                            onerror="this.onerror=null; this.src='{{ asset('assets/img/icon-light.png') }}';"
                                            alt="{{ $withdrawal->name }}">
                                    </div>
                                    <div class="d-flex flex-column text-start align-items-start">
                                        <span class="mb-0 small fw-semibold text-white">{{ $withdrawal->user->username ?? 'Member' }}</span>
                                        <h6 class="text-secondary pb-0 mb-0" style="font-size: 11px;">{{ $withdrawal->updated_at ? $withdrawal->updated_at->diffForHumans() : 'Just now' }}</h6>
                                    </div>

                                    <div class="p-1">
                                        <span
                                            class="rounded-pill badge live-cashout-badge d-flex align-items-center gap-1 px-2 py-1"
                                            :style="is_coin == '1' ? 'line-height: 0' : ''">
                                            <img src="{{ asset('assets/img/coin.png') }}?v=2"
                                                 x-show="is_coin == '1'"
                                                 width="14px"
                                                 height="14px"
                                                 style="object-fit: contain;"
                                                 alt="ERC">
                                            <span
                                                x-text="is_coin == '1' ? '{{ number_format($withdrawal->amount) }} ERC' : '{{ '$' . to_money_str($withdrawal->amount) }}'"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    window.addEventListener('render-live-cashouts', () => {
        try {
            document.querySelectorAll('.tooltip').forEach((el) => {
                el.remove();
            });

            setTimeout(() => {
                try {
                    document.querySelectorAll('[tooltip="true"]').forEach((el) => {
                        new bootstrap.Tooltip(el);
                    });
                } catch (e) {
                    console.error(e);
                }
            }, 100);

        } catch (e) {
            console.error(e);
        }
    });
</script>
@endscript
