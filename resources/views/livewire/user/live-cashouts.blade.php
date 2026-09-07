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

    /* Hide horizontal scrollbar for sleek ticker aesthetics */
    .live-stream-container::-webkit-scrollbar {
        display: none !important;
        height: 0px !important;
    }
    .live-stream-container {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }
</style>
@endassets

<div wire:poll.20s>
    <div class="container-fluid"
         x-data="{
             isPaused: false,
             isModalOpen: false,
             speed: 35,
             rafId: null,
             lastTimestamp: null,
             pauseTimeout: null,

             init() {
                 this.startAutoScroll();

                 // Pause while Bootstrap 5 activityModal is open, resume when closed
                 window.addEventListener('hidden.bs.modal', (e) => {
                     if (!e.target || e.target.id === 'activityModal') {
                         this.isModalOpen = false;
                         this.resume();
                     }
                 });

                 window.addEventListener('shown.bs.modal', (e) => {
                     if (!e.target || e.target.id === 'activityModal') {
                         this.isModalOpen = true;
                         this.pause();
                     }
                 });

                 // Livewire modal closed event
                 window.addEventListener('activity-modal-closed', () => {
                     this.isModalOpen = false;
                     this.resume();
                 });
             },

             startAutoScroll() {
                 if (this.rafId) {
                     cancelAnimationFrame(this.rafId);
                 }

                 const loop = (timestamp) => {
                     if (!this.lastTimestamp) this.lastTimestamp = timestamp;
                     const rawDelta = (timestamp - this.lastTimestamp) / 1000;
                     this.lastTimestamp = timestamp;

                     // Clamp delta to prevent huge leap if tab was backgrounded
                     const delta = Math.min(rawDelta, 0.05);

                     if (!this.isPaused && !this.isModalOpen && this.$refs.stream) {
                         const el = this.$refs.stream;
                         const halfWidth = el.scrollWidth / 2;

                         if (halfWidth > 0) {
                             el.scrollLeft += this.speed * delta;

                             // Seamless infinite wrap around to first set
                             if (el.scrollLeft >= halfWidth) {
                                 el.scrollLeft -= halfWidth;
                             }
                         }
                     }

                     this.rafId = requestAnimationFrame(loop);
                 };

                 this.rafId = requestAnimationFrame(loop);
             },

             pause() {
                 this.isPaused = true;
             },

             resume() {
                 if (!this.isModalOpen) {
                     this.isPaused = false;
                 }
             },

             pauseTemp(seconds = 3) {
                 this.pause();
                 clearTimeout(this.pauseTimeout);
                 this.pauseTimeout = setTimeout(() => {
                     this.resume();
                 }, seconds * 1000);
             },

             scrollLeft() {
                 this.pauseTemp(3);
                 if (!this.$refs.stream) return;
                 const el = this.$refs.stream;
                 const halfWidth = el.scrollWidth / 2;
                 if (el.scrollLeft <= 10 && halfWidth > 0) {
                     el.scrollLeft += halfWidth;
                 }
                 el.scrollBy({ left: -260, behavior: 'smooth' });
             },

             scrollRight() {
                 this.pauseTemp(3);
                 if (!this.$refs.stream) return;
                 const el = this.$refs.stream;
                 const halfWidth = el.scrollWidth / 2;
                 if (el.scrollLeft >= halfWidth && halfWidth > 0) {
                     el.scrollLeft -= halfWidth;
                 }
                 el.scrollBy({ left: 260, behavior: 'smooth' });
             },

             onWheel(event) {
                 if (!this.$refs.stream) return;
                 this.pauseTemp(3);
                 this.$refs.stream.scrollLeft += event.deltaY;
             },

             onCardClick(userId, leadId = null) {
                 this.isPaused = true;
                 this.isModalOpen = true;

                 // Clean up tooltips so none linger after modal opens
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

            <!-- Scroll Left Arrow Button -->
            <button class="btn btn-sm btn-dark d-none d-md-flex align-items-center justify-content-center p-0 flex-shrink-0 rounded-circle"
                    style="width: 28px; height: 28px; background: var(--bs-card-bg); border: 1px solid rgba(255,255,255,0.08);"
                    type="button"
                    @click="scrollLeft()"
                    title="Scroll Left">
                <i class="fa-solid fa-chevron-left text-secondary" style="font-size: 10px;"></i>
            </button>

            <!-- Scrollable Stream (Smooth Auto-Scrolling with Infinite Loop) -->
            <div class="d-flex align-items-center overflow-x-auto flex-grow-1 py-1 px-1 live-stream-container"
                 x-ref="stream"
                 wire:ignore.self
                 style="gap: 8px; scroll-behavior: auto; scrollbar-width: none; -ms-overflow-style: none; -webkit-overflow-scrolling: touch;"
                 @mouseenter="pause()"
                 @mouseleave="resume()"
                 @wheel.prevent="onWheel($event)"
                 @touchstart="pause()"
                 @touchend="pauseTemp(2)">
                @if(count($cashouts) > 0)
                    @for($cloneIndex = 0; $cloneIndex < 2; $cloneIndex++)
                        @foreach($cashouts as $withdrawal)
                            <div class="card flex-shrink-0 border-0 text-white px-3 py-1"
                                 style="cursor: pointer; min-width: 210px; max-width: 260px; background: var(--bs-card-bg); border: 1px solid rgba(255,255,255,0.06) !important; border-radius: 10px; transition: transform 0.2s ease, border-color 0.2s ease; user-select: none;"
                                 onmouseover="this.style.borderColor='rgba(55,231,128,0.35)'; this.style.transform='translateY(-1px)';"
                                 onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'; this.style.transform='translateY(0)';"
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
                @else
                    <div class="text-secondary small px-3 py-2">Waiting for live activities...</div>
                @endif
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
