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
    <div class="container-fluid" x-data="{
        scrollLeft() {
            this.$refs.stream.scrollBy({ left: -260, behavior: 'smooth' });
        },
        scrollRight() {
            this.$refs.stream.scrollBy({ left: 260, behavior: 'smooth' });
        }
    }">
        <div class="d-flex align-items-center mt-2 position-relative gap-2">
            <!-- Pinned Live Indicator Badge -->
            <div class="d-flex align-items-center flex-shrink-0 px-3 py-2 rounded-3 border-0 shadow-sm"
                 style="background: var(--bs-card-bg); height: 48px; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="position-relative me-2 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px;">
                    <span class="live-bg" style="background: rgba(55, 231, 128, 0.35);"></span>
                    <span class="rounded-circle" style="width: 8px; height: 8px; background: #37E780; display: inline-block;"></span>
                </div>
                <span class="fw-bold text-white small" style="letter-spacing: 0.5px; font-size: 12px;">LIVE</span>
            </div>

            <!-- Scroll Left Arrow Button -->
            <button class="btn btn-sm btn-dark d-none d-md-flex align-items-center justify-content-center p-0 flex-shrink-0 rounded-circle"
                    style="width: 28px; height: 28px; background: var(--bs-card-bg); border: 1px solid rgba(255,255,255,0.08);"
                    type="button"
                    @click="scrollLeft()"
                    title="Scroll Left">
                <i class="fa-solid fa-chevron-left text-secondary" style="font-size: 10px;"></i>
            </button>

            <!-- Scrollable Stream (Latest Pinned on Left, No Auto-Disappearing) -->
            <div class="d-flex align-items-center overflow-x-auto flex-grow-1 py-1 px-1"
                 x-ref="stream"
                 style="gap: 8px; scroll-behavior: smooth; scrollbar-width: thin; -webkit-overflow-scrolling: touch;"
                 @wheel.prevent="$refs.stream.scrollLeft += $event.deltaY">
                @forelse($cashouts as $withdrawal)
                    <div class="card flex-shrink-0 border-0 text-white px-3 py-1"
                         style="cursor: pointer; min-width: 210px; max-width: 260px; background: var(--bs-card-bg); border: 1px solid rgba(255,255,255,0.06) !important; border-radius: 10px; transition: transform 0.2s ease, border-color 0.2s ease;"
                         onmouseover="this.style.borderColor='rgba(55,231,128,0.35)'; this.style.transform='translateY(-1px)';"
                         onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.transform='translateY(0)';"
                         tooltip="true"
                         data-bs-html="true"
                         data-bs-placement="bottom"
                         title="<div class='text-start text-body'><p class='m-0'><strong>User:</strong> {{ $withdrawal->user->username ?? 'Anonymous' }}</p><p class='m-0'><strong>Activity:</strong> {{ $withdrawal->name }}</p><p class='m-0'><strong>Reward:</strong> {{ number_format($withdrawal->amount) }} ERC</p></div>"
                         @click="$dispatch('activity-open', {user_id: '{{ $withdrawal->user_id }}'})">
                        <div class="d-flex align-items-center gap-2">
                            <div class="d-flex justify-content-center rounded-circle align-items-center overflow-hidden flex-shrink-0"
                                 style="width: 28px; height: 28px; background-color: {{ $withdrawal->bg_color ? $withdrawal->bg_color . ' !important' : 'rgba(255,255,255,0.08)' }}">
                                <img height="100%"
                                     width="100%"
                                     @class(['object-fit-contain', 'p-1' => $withdrawal instanceof \App\Models\CashoutRequest])
                                     src="{{ $withdrawal->method_image ? \Storage::url($withdrawal->method_image) : ($withdrawal->user ? $withdrawal->user->avatar() : asset('assets/img/icon-light.png')) }}"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/img/icon-light.png') }}';"
                                     alt="{{ $withdrawal->name }}">
                            </div>

                            <div class="d-flex flex-column text-start" style="min-width: 75px; max-width: 110px;">
                                <span class="mb-0 text-truncate text-white fw-semibold" style="font-size: 12px;">{{ $withdrawal->user->username ?? 'Member' }}</span>
                                <span class="text-secondary text-truncate" style="font-size: 10px;">{{ $withdrawal->created_at ? $withdrawal->created_at->diffForHumans(null, true, true) : 'Just now' }}</span>
                            </div>

                            <div class="ms-auto flex-shrink-0">
                                <span class="rounded-pill badge live-cashout-badge d-flex align-items-center gap-1 px-2 py-1"
                                      :style="is_coin == '1' ? 'line-height: 0' : ''">
                                    <img src="{{ asset('assets/img/coin.png') }}?v=2"
                                         x-show="is_coin == '1'"
                                         width="13px"
                                         height="13px"
                                         style="object-fit: contain;"
                                         alt="ERC">
                                    <span style="font-size: 11px; font-weight: 600;"
                                          x-text="is_coin == '1' ? '{{ number_format($withdrawal->amount) }}' : '{{ '$' . to_money_str($withdrawal->amount) }}'"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-secondary small px-3 py-2">Waiting for live activities...</div>
                @endforelse
            </div>

            <!-- Scroll Right Arrow Button -->
            <button class="btn btn-sm btn-dark d-none d-md-flex align-items-center justify-content-center p-0 flex-shrink-0 rounded-circle"
                    style="width: 28px; height: 28px; background: var(--bs-card-bg); border: 1px solid rgba(255,255,255,0.08);"
                    type="button"
                    @click="scrollRight()"
                    title="Scroll Right">
                <i class="fa-solid fa-chevron-right text-secondary" style="font-size: 10px;"></i>
            </button>
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
