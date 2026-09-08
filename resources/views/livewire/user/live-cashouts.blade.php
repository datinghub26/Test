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

    .live-icon {
        animation: pulse 1.5s infinite ease-in-out;
        color: var(--bs-primary) !important;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.8; }
        50% { transform: scale(1.18); opacity: 1; }
        100% { transform: scale(1); opacity: 0.8; }
    }

    /* PaidCash-style pill badge */
    .live-cashout-badge {
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #f1f5f9 !important;
        font-weight: 600;
    }

    /* Track styles: auto & manual grab-to-scroll */
    .live-activity-container {
        position: relative;
        width: 100%;
    }

    .live-stream-viewport {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
        cursor: grab;
        user-select: none;
        -webkit-user-select: none;
        scrollbar-width: none;
        -ms-overflow-style: none;
        scroll-behavior: auto;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
        overscroll-behavior-x: contain;
    }

    .live-stream-viewport::-webkit-scrollbar {
        display: none !important;
        height: 0 !important;
    }

    .live-stream-viewport.is-grabbing {
        cursor: grabbing !important;
    }

    .live-stream-viewport.is-grabbing .live-card-item {
        pointer-events: none !important;
    }

    .live-stream-track {
        display: flex;
        width: max-content;
        gap: 8px;
        align-items: center;
        transform: translateZ(0);
        will-change: scroll-position;
    }

    /* PaidCash-style live feed card */
    .live-card-item {
        cursor: pointer;
        min-width: 205px;
        max-width: 250px;
        background: #171a23 !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 8px;
        transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        flex-shrink: 0;
    }

    .live-card-item:hover,
    .live-card-item.active-card {
        border-color: rgba(56, 189, 248, 0.5) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
    }

    /* Floating Details Popover (Matching PaidCash Reference Image) */
    .activity-popover-box {
        position: absolute;
        z-index: 1060;
        background: #131722;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 10px;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.8), 0 0 0 1px rgba(255, 255, 255, 0.04);
        padding: 13px 16px;
        width: 270px;
        max-width: 90vw;
    }

    .activity-popover-arrow {
        position: absolute;
        top: -6px;
        left: 50%;
        transform: translateX(-50%) rotate(45deg);
        width: 11px;
        height: 11px;
        background: #131722;
        border-top: 1px solid rgba(255, 255, 255, 0.12);
        border-left: 1px solid rgba(255, 255, 255, 0.12);
    }

    /* Real-time Prepend Entry Animation */
    @keyframes liveItemPrepend {
        0% {
            opacity: 0;
            transform: translateX(-30px) scale(0.88);
            max-width: 0;
            padding: 0;
            margin: 0;
        }
        100% {
            opacity: 1;
            transform: translateX(0) scale(1);
            max-width: 250px;
        }
    }

    .activity-is-new {
        animation: liveItemPrepend 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        border-color: #37E780 !important;
        box-shadow: 0 0 16px rgba(55, 231, 128, 0.4) !important;
    }
</style>
@endassets

<div>
    <div class="container-fluid" x-data="liveActivityTicker()" wire:poll.15s="checkNewActivity">
        <div class="d-flex align-items-center mt-2 position-relative gap-2 live-activity-container"
             x-ref="container">
            <!-- Pinned Live Indicator Badge -->
            <div class="d-flex align-items-center flex-shrink-0 px-3 py-2 rounded-3 border-0 shadow-sm"
                 style="background: #171a23; height: 48px; border: 1px solid rgba(255,255,255,0.08) !important;">
                <div class="position-relative me-2 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px;">
                    <span class="live-bg" style="background: rgba(55, 231, 128, 0.35);"></span>
                    <span class="rounded-circle" style="width: 8px; height: 8px; background: #37E780; display: inline-block;"></span>
                </div>
                <span class="fw-bold text-white small" style="letter-spacing: 0.5px; font-size: 12px;">LIVE</span>
            </div>

            <!-- Scrollable & Draggable Live Feed Track -->
            <div class="live-stream-viewport flex-grow-1 py-1 px-1"
                 x-ref="viewport"
                 :class="{ 'is-grabbing': isDown }"
                 @pointerdown="onPointerDown($event)"
                 @pointermove="onPointerMove($event)"
                 @pointerup="onPointerUp($event)"
                 @pointercancel="onPointerCancel($event)"
                 @wheel.prevent="onWheel($event)"
                 @scroll.passive="onScroll()">
                <div class="live-stream-track" x-ref="track" wire:ignore.self>
                    {{-- Prepend items (dynamically added at first on left side) --}}
                    <template x-for="item in prependItems" :key="'prep-' + item.type + '-' + item.id">
                        <div class="card live-card-item text-white px-3 py-1 activity-is-new"
                             :class="{ 'active-card': activeCard && activeCard.id == item.id && activeCard.type == item.type }"
                             @click="onDataCardClick(item, $event.currentTarget)">
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex justify-content-center rounded-2 align-items-center overflow-hidden flex-shrink-0"
                                     style="width: 28px; height: 28px;"
                                     :style="item.bg_color ? `background-color: ${item.bg_color} !important;` : 'background-color: rgba(255,255,255,0.08);'">
                                    <img height="100%"
                                         width="100%"
                                         class="object-fit-contain"
                                         :src="item.image || item.avatar"
                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/icon-light.png') }}';"
                                         :alt="item.wall">
                                </div>

                                <div class="d-flex flex-column text-start overflow-hidden" style="min-width: 80px; max-width: 125px;">
                                    <span class="mb-0 text-truncate text-white fw-semibold" style="font-size: 12px; line-height: 1.2;" x-text="item.wall"></span>
                                    <span class="text-secondary text-truncate" style="font-size: 11px; line-height: 1.2;" x-text="item.username"></span>
                                </div>

                                <div class="ms-auto flex-shrink-0">
                                    <span class="rounded-pill badge live-cashout-badge d-flex align-items-center gap-1 px-2 py-1">
                                        <img src="{{ asset('assets/img/coin.png') }}?v=2" x-show="is_coin !== '0'" width="13px" height="13px" style="object-fit: contain;" alt="ERC">
                                        <span style="font-size: 11px;" x-text="formatCoins(item.amount)"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Server Rendered Blade Items (strictly 1 set, max 20) --}}
                    @if(count($activities) > 0)
                        @foreach($activities as $item)
                            <div class="card live-card-item text-white px-3 py-1"
                                 data-id="{{ $item['id'] }}"
                                 data-type="{{ $item['type'] }}"
                                 data-user-id="{{ $item['user_id'] }}"
                                 data-username="{{ $item['username'] }}"
                                 data-avatar="{{ $item['avatar'] }}"
                                 data-wall="{{ $item['wall'] }}"
                                 data-offer="{{ $item['offer'] }}"
                                 data-amount="{{ $item['amount'] }}"
                                 :class="{ 'active-card': activeCard && activeCard.id == '{{ $item['id'] }}' && activeCard.type == '{{ $item['type'] }}' }"
                                 @click="onCardClick($event.currentTarget)">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-flex justify-content-center rounded-2 align-items-center overflow-hidden flex-shrink-0"
                                         style="width: 28px; height: 28px; {{ $item['bg_color'] ? 'background-color: ' . $item['bg_color'] . ' !important;' : 'background-color: rgba(255,255,255,0.08);' }}">
                                        <img height="100%"
                                             width="100%"
                                             class="object-fit-contain"
                                             src="{{ $item['image'] ?: $item['avatar'] }}"
                                             onerror="this.onerror=null; this.src='{{ asset('assets/img/icon-light.png') }}';"
                                             alt="{{ $item['wall'] }}">
                                    </div>

                                    <div class="d-flex flex-column text-start overflow-hidden" style="min-width: 80px; max-width: 125px;">
                                        <span class="mb-0 text-truncate text-white fw-semibold" style="font-size: 12px; line-height: 1.2;">{{ $item['wall'] }}</span>
                                        <span class="text-secondary text-truncate" style="font-size: 11px; line-height: 1.2;">{{ $item['username'] }}</span>
                                    </div>

                                    <div class="ms-auto flex-shrink-0">
                                        <span class="rounded-pill badge live-cashout-badge d-flex align-items-center gap-1 px-2 py-1">
                                            <img src="{{ asset('assets/img/coin.png') }}?v=2" x-show="is_coin !== '0'" width="13px" height="13px" style="object-fit: contain;" alt="ERC">
                                            <span style="font-size: 11px;" x-text="formatCoins('{{ $item['amount'] }}')">{{ number_format($item['amount']) }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Floating Details Popover (Matching PaidCash Reference Image) -->
            <div class="activity-popover-box"
                 x-show="activeCard"
                 x-cloak
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0 transform -translate-y-2 scale-95"
                 x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 transform -translate-y-2 scale-95"
                 @click.outside="closePopover()"
                 :style="`position: absolute; top: ${popoverTop}px; left: ${popoverLeft}px; transform: translateX(-50%); z-index: 1060;`">

                <!-- Arrow pointing to card -->
                <div class="activity-popover-arrow" :style="`left: ${arrowLeft};`"></div>

                <div class="d-flex flex-column gap-2" style="font-size: 12px;">
                    <!-- Wall Row -->
                    <div class="d-flex align-items-baseline gap-2">
                        <span style="min-width: 54px; color: #8a99ad; font-weight: 500;">Wall:</span>
                        <span style="color: #38bdf8; font-weight: 600;" x-text="activeCard?.wall"></span>
                    </div>

                    <!-- Offer Row -->
                    <div class="d-flex align-items-baseline gap-2">
                        <span style="min-width: 54px; color: #8a99ad; font-weight: 500;">Offer:</span>
                        <span class="text-break" style="color: #38bdf8; font-weight: 400;" x-text="activeCard?.offer"></span>
                    </div>

                    <!-- Amount Row -->
                    <div class="d-flex align-items-center gap-2">
                        <span style="min-width: 54px; color: #8a99ad; font-weight: 500;">Amount:</span>
                        <span class="d-inline-flex align-items-center gap-1" style="color: #38bdf8; font-weight: 600;">
                            <img src="{{ asset('assets/img/coin.png') }}?v=2" x-show="is_coin !== '0'" width="13px" height="13px" alt="ERC">
                            <span x-text="formatReward(activeCard?.amount)"></span>
                        </span>
                    </div>

                    <!-- Member Footer with View Profile Button -->
                    <div class="pt-2 mt-1 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-1 text-secondary small">
                            <img :src="activeCard?.avatar" class="rounded-circle" width="18" height="18" onerror="this.onerror=null; this.src='{{ asset('assets/img/icon-light.png') }}';">
                            <span class="text-truncate" style="max-width: 100px;" x-text="activeCard?.username"></span>
                        </div>
                        <button type="button" class="btn btn-xs btn-outline-info py-0 px-2 fw-semibold" style="font-size: 10px;"
                                @click="$dispatch('activity-open', { user_id: activeCard?.user_id, lead_id: (activeCard?.type === 'lead' ? activeCard?.id : null) }); closePopover();">
                            View Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    function initLiveTicker() {
        if (typeof Alpine === 'undefined') return;

        Alpine.data('liveActivityTicker', () => ({
            is_coin: (typeof localStorage !== 'undefined' ? localStorage.getItem('isCoin') || '1' : '1'),
            isDown: false,
            isDragging: false,
            startX: 0,
            scrollStart: 0,
            lastX: 0,
            lastTime: 0,
            velocity: 0,
            momentumRaf: null,
            activeCard: null,
            popoverLeft: 0,
            popoverTop: 0,
            arrowLeft: '50%',
            prependItems: [],

            init() {
                window.addEventListener('live-activity-prepend', (e) => {
                    this.prependActivity(e.detail?.newActivity);
                });

                window.addEventListener('update-coins', (e) => {
                    if (e.detail && typeof e.detail.isCoin !== 'undefined') {
                        this.is_coin = String(e.detail.isCoin);
                    }
                });

                window.addEventListener('resize', () => {
                    if (this.activeCard) this.closePopover();
                });

                window.addEventListener('hidden.bs.modal', (e) => {
                    if (!e.target || e.target.id === 'activityModal') {
                        this.closePopover();
                    }
                });

                window.addEventListener('activity-modal-closed', () => {
                    this.closePopover();
                });
            },

            formatCoins(val) {
                const num = Number(val) || 0;
                if (this.is_coin === '0') {
                    return '$' + (num / 1000).toFixed(2);
                }
                return num.toLocaleString();
            },

            formatReward(val) {
                const num = Number(val) || 0;
                if (this.is_coin === '0') {
                    return '$' + (num / 1000).toFixed(2);
                }
                return num.toLocaleString() + ' ERC';
            },

            stopMomentum() {
                if (this.momentumRaf) {
                    cancelAnimationFrame(this.momentumRaf);
                    this.momentumRaf = null;
                }
                this.velocity = 0;
            },

            startMomentum() {
                this.stopMomentum();
                if (Math.abs(this.velocity) < 0.08 || !this.$refs.viewport) return;

                const vp = this.$refs.viewport;
                const decay = 0.93;
                const step = () => {
                    if (Math.abs(this.velocity) < 0.04) {
                        this.velocity = 0;
                        this.momentumRaf = null;
                        return;
                    }
                    vp.scrollLeft -= this.velocity * 16;
                    this.velocity *= decay;
                    this.momentumRaf = requestAnimationFrame(step);
                };
                this.momentumRaf = requestAnimationFrame(step);
            },

            onPointerDown(e) {
                if (e.button !== 0 && e.pointerType === 'mouse') return;
                if (!this.$refs.viewport) return;

                this.stopMomentum();
                this.isDown = true;
                this.isDragging = false;
                this.startX = e.clientX;
                this.lastX = e.clientX;
                this.lastTime = performance.now();
                this.scrollStart = this.$refs.viewport.scrollLeft;
                this.velocity = 0;

                try {
                    e.currentTarget.setPointerCapture(e.pointerId);
                } catch (err) {}
            },

            onPointerMove(e) {
                if (!this.isDown || !this.$refs.viewport) return;

                const currentX = e.clientX;
                const now = performance.now();
                const walk = currentX - this.startX;

                if (Math.abs(walk) > 4) {
                    this.isDragging = true;
                    if (this.activeCard) this.closePopover();
                }

                this.$refs.viewport.scrollLeft = this.scrollStart - walk;

                const dt = Math.max(now - this.lastTime, 8);
                const dx = currentX - this.lastX;
                const instantVel = dx / dt;
                this.velocity = (this.velocity * 0.3) + (instantVel * 0.7);

                this.lastX = currentX;
                this.lastTime = now;
            },

            onPointerUp(e) {
                if (!this.isDown) return;
                this.isDown = false;

                try {
                    if (e.currentTarget.hasPointerCapture(e.pointerId)) {
                        e.currentTarget.releasePointerCapture(e.pointerId);
                    }
                } catch (err) {}

                this.startMomentum();

                setTimeout(() => {
                    this.isDragging = false;
                }, 80);
            },

            onPointerCancel(e) {
                this.onPointerUp(e);
            },

            onWheel(e) {
                if (!this.$refs.viewport) return;
                this.stopMomentum();
                if (this.activeCard) this.closePopover();

                const delta = e.deltaX !== 0 ? e.deltaX : e.deltaY;
                this.$refs.viewport.scrollBy({ left: delta * 0.85, behavior: 'smooth' });
            },

            onScroll() {
                if (this.activeCard && this.isDragging) {
                    this.closePopover();
                }
            },

            onCardClick(cardEl) {
                if (this.isDragging) return;

                const id = cardEl.dataset.id;
                const type = cardEl.dataset.type;

                if (this.activeCard && this.activeCard.id === id && this.activeCard.type === type) {
                    this.closePopover();
                    return;
                }

                this.activeCard = {
                    id: id,
                    type: type,
                    user_id: cardEl.dataset.userId,
                    username: cardEl.dataset.username,
                    avatar: cardEl.dataset.avatar,
                    wall: cardEl.dataset.wall,
                    offer: cardEl.dataset.offer,
                    amount: cardEl.dataset.amount
                };

                this.$nextTick(() => {
                    this.positionPopover(cardEl);
                });
            },

            onDataCardClick(item, cardEl) {
                if (this.isDragging) return;

                if (this.activeCard && this.activeCard.id === item.id && this.activeCard.type === item.type) {
                    this.closePopover();
                    return;
                }

                this.activeCard = item;
                this.$nextTick(() => {
                    this.positionPopover(cardEl);
                });
            },

            positionPopover(cardEl) {
                if (!cardEl || !this.$refs.container) return;
                const cardRect = cardEl.getBoundingClientRect();
                const containerRect = this.$refs.container.getBoundingClientRect();

                const cardCenter = (cardRect.left - containerRect.left) + (cardRect.width / 2);
                const popoverWidth = 270;
                const halfWidth = popoverWidth / 2;

                const minLeft = halfWidth + 8;
                const maxLeft = containerRect.width - halfWidth - 8;
                const clampedCenter = Math.max(minLeft, Math.min(maxLeft, cardCenter));

                this.popoverLeft = clampedCenter;
                this.popoverTop = (cardRect.bottom - containerRect.top) + 8;

                const arrowOffset = cardCenter - (clampedCenter - halfWidth);
                this.arrowLeft = `${Math.max(16, Math.min(popoverWidth - 16, arrowOffset))}px`;
            },

            closePopover() {
                this.activeCard = null;
            },

            prependActivity(data) {
                if (!data) return;
                const exists = this.prependItems.some(i => i.id == data.id && i.type == data.type);
                if (exists) return;

                this.prependItems.unshift(data);
                if (this.prependItems.length > 20) {
                    this.prependItems.pop();
                }

                this.$nextTick(() => {
                    const track = this.$refs.track;
                    if (track) {
                        const cards = track.querySelectorAll('.live-card-item');
                        if (cards.length > 20) {
                            for (let i = 20; i < cards.length; i++) {
                                cards[i].remove();
                            }
                        }
                    }
                });

                if (this.$refs.viewport) {
                    this.$refs.viewport.scrollTo({ left: 0, behavior: 'smooth' });
                }
            }
        }));
    }

    if (window.Alpine) {
        initLiveTicker();
    } else {
        document.addEventListener('alpine:init', initLiveTicker);
    }
})();
</script>
