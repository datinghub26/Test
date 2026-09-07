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

    /* Continuous Live Ticker Glide Animation */
    @keyframes liveTickerGlide {
        0% {
            transform: translate3d(0, 0, 0);
        }
        100% {
            transform: translate3d(-50%, 0, 0);
        }
    }

    .live-ticker-container {
        overflow: hidden;
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
        mask-image: linear-gradient(to right, transparent, black 12px, black calc(100% - 12px), transparent);
        -webkit-mask-image: linear-gradient(to right, transparent, black 12px, black calc(100% - 12px), transparent);
    }

    .live-ticker-track {
        display: flex;
        width: max-content;
        gap: 8px;
        will-change: transform;
        animation: liveTickerGlide 65s linear infinite;
    }

    /* Pause immediately on hover or when paused by modal */
    .live-ticker-track:hover,
    .live-ticker-track.is-paused {
        animation-play-state: paused !important;
    }

    .live-card-item {
        cursor: pointer;
        min-width: 210px;
        max-width: 260px;
        background: var(--bs-card-bg);
        border: 1px solid rgba(255, 255, 255, 0.06) !important;
        border-radius: 10px;
        transition: transform 0.2s ease, border-color 0.2s ease;
        user-select: none;
        flex-shrink: 0;
    }

    .live-card-item:hover {
        border-color: rgba(55, 231, 128, 0.4) !important;
        transform: translateY(-1px);
    }
</style>
@endassets

<div wire:poll.30s>
    <div class="container-fluid"
         x-data="{
             isPaused: false,
             isModalOpen: false,

             init() {
                 // Resume moving when activity modal closes
                 window.addEventListener('hidden.bs.modal', (e) => {
                     if (!e.target || e.target.id === 'activityModal') {
                         this.isModalOpen = false;
                         this.isPaused = false;
                     }
                 });

                 // Pause moving when activity modal opens
                 window.addEventListener('shown.bs.modal', (e) => {
                     if (!e.target || e.target.id === 'activityModal') {
                         this.isModalOpen = true;
                         this.isPaused = true;
                     }
                 });

                 // Handle Livewire event
                 window.addEventListener('activity-modal-closed', () => {
                     this.isModalOpen = false;
                     this.isPaused = false;
                 });
             },

             onCardClick(userId, leadId = null) {
                 // Stop moving immediately
                 this.isPaused = true;
                 this.isModalOpen = true;

                 // Clean up any visible tooltip so none linger
                 try {
                     document.querySelectorAll('.tooltip').forEach(el => el.remove());
                 } catch(e) {}

                 $dispatch('activity-open', { user_id: userId, lead_id: leadId });
                 if (typeof Livewire !== 'undefined') {
                     Livewire.dispatch('activity-open', { user_id: userId, lead_id: leadId });
                 }
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

            <!-- Auto-Gliding Live Stream Container -->
            <div class="live-ticker-container flex-grow-1 py-1 px-1">
                @if(count($cashouts) > 0)
                    <div class="live-ticker-track"
                         wire:ignore.self
                         style="animation-duration: {{ max(35, count($cashouts) * 3.5) }}s;"
                         :class="{ 'is-paused': isPaused || isModalOpen }"
                         @mouseenter="isPaused = true"
                         @mouseleave="if (!isModalOpen) isPaused = false"
                         @touchstart="isPaused = true"
                         @touchend="setTimeout(() => { if (!isModalOpen) isPaused = false }, 2000)">
                        @for($cloneIndex = 0; $cloneIndex < 2; $cloneIndex++)
                            @foreach($cashouts as $withdrawal)
                                <div class="card live-card-item text-white px-3 py-1"
                                     tooltip="true"
                                     data-bs-html="true"
                                     data-bs-placement="bottom"
                                     title="<div class='text-start py-1 px-1' style='font-size: 11px;'><div class='fw-bold text-white mb-1'><i class='fa-solid fa-user me-1 text-primary'></i> {{ addslashes($withdrawal->user->username ?? 'Member') }}</div><div class='text-light mb-1'><i class='fa-solid fa-circle-check me-1 text-success'></i> {{ addslashes($withdrawal->name) }}</div><div class='text-warning fw-semibold'><i class='fa-solid fa-coins me-1'></i> {{ number_format($withdrawal->amount) }} ERC</div></div>"
                                     @click="onCardClick('{{ $withdrawal->user_id }}', '{{ ($withdrawal->item_type ?? '') === 'lead' ? $withdrawal->id : '' }}')">
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
                            @endforeach
                        @endfor
                    </div>
                @else
                    <div class="text-secondary small px-3 py-2">Waiting for live activities...</div>
                @endif
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
                        const existing = bootstrap.Tooltip.getInstance(el);
                        if (existing) existing.dispose();
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
