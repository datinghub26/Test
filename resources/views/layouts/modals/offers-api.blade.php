<div
    x-data="{ offer: null, is_coin: localStorage.getItem('isCoin') || '0' }"
    x-on:update-coins.window="is_coin = $event.detail.isCoin"
    x-on:open-offer-api-modal.window="
        offer = $event.detail;
        $nextTick(() => {
            $('#offerApiModal').modal('show');
        });
    "
    wire:ignore.self
    class="modal fade"
    tabindex="-1"
    role="dialog"
    aria-hidden="true"
    id="offerApiModal"
    style="z-index: 9999999"
>

    <div class="modal-dialog modal-dialog-scrollable modal-md modal-fullscreen-sm-down modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title text-body f-md" x-text="window.decodeHtml(offer?.title || 'Loading...')"></h4>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Loading Indicator -->
                <div x-show="!offer" class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading info...</p>
                </div>

                <!-- Offer Details -->
                <div x-show="offer" class="h-100">

                    <!-- Offer Details -->
                    <div x-show="offer" class="d-flex flex-column h-100">
                        <div class="flex-grow-1 overflow-auto">

                            <!-- Offer Header -->
                            <div class="d-flex">
                                <div class="position-relative">
                                    <img :src="offer?.image || ''" width="149"
                                         style="aspect-ratio: 1 / 1; border-radius: 10px" alt="">

                                    <!-- Devices -->
                                    <div class="badge bg-label-dark position-absolute"
                                         style="top: 3px; right: 3px; padding: 5px 8px; background-color: #18212fc9 !important">
                                        <div class="gap-2 d-flex align-items-center justify-content-center">
                                    <span class="text-white fa-brands fa-android"
                                          x-show="offer?.devices && offer?.devices.includes('Android')"></span>
                                            <span class="text-white fa-brands fa-apple"
                                                  x-show="offer?.devices && offer?.devices.includes('iOS')"></span>
                                            <span class="text-white fa-solid fa-laptop"
                                                  x-show="(offer?.devices && offer?.devices.includes('Desktop')) || (offer?.devices.length === 0)"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="gap-2 d-flex flex-column justify-content-start ms-3">
                                    <!-- Provider -->
                                    <span class="d-flex align-items-center badge bg-primary">
                                        <i class="fa-solid fa-rocket me-2"></i>
                                        <span x-text="offer?.provider || ''"></span>
                                    </span>

                                    <!-- Points or Coins -->
                                    <div>
                                        <span class="badge bg-dark">
                                            <template x-if="is_coin == '0'">
                                                <span class="d-flex align-items-end fw-bold"
                                                      style="font-size: 15px; line-height: 16px">
                                                    $<span x-text="Math.floor(offer?.points / 1000)"></span>.
                                                    <span style="font-size: 10px; line-height: 12px" class="fw-bold"
                                                          x-text="(offer?.points / 1000).toFixed(2).substring(2)"></span>
                                                </span>
                                            </template>
                                            <template x-if="is_coin == '1'">
                                                <span class="d-flex align-items-center fw-medium"
                                                      style="font-size: 13px; line-height: 0">
                                                    <img src="{{ asset('assets/img/coin.png') }}" alt="" width="12px"
                                                         class="me-1">
                                                    <span x-text="Number(offer?.points).toLocaleString()"></span>
                                                </span>
                                            </template>
                                        </span>
                                    </div>

                                    <!-- Categories -->
                                    <span class="badge bg-dark">
                                        <span x-text="offer?.categories ? offer?.categories[0] : ''"></span>
                                    </span>

                                    <!-- Status -->
{{--                                    <span class="badge bg-label-primary"--}}
{{--                                          x-bind:class="{--}}
{{--                                                'bg-label-sky': offer?.status === 'Started',--}}
{{--                                                'bg-success': offer?.status === 'Completed',--}}
{{--                                                'bg-label-primary': offer?.status === 'Not Started',--}}
{{--                                            }">--}}
{{--                                            <span x-text="offer?.status || ''"></span>--}}
{{--                                    </span>--}}
                                </div>
                            </div>

                            <!-- Start Offer Button -->
                            <div class="mt-auto mt-md-4">
                                <a class="btn btn-primary w-100"
                                   :href="offer?.link ? offer?.link.replace('{user_id}', '{{ auth()->id() }}') : '#'"
                                   target="_blank">
                                    <i class="fa-solid fa-play me-2"></i>
                                    Start Offer
                                </a>
                            </div>

                            <!-- Description -->
                            <div class="mt-4">
                                <div class="mt-2 px-4 p-2  bg-opacity-50 bg-dark" style="border-radius: 20px;">
                                    <p class="text-center text-body m-0 p-0" style="font-size: 11px"
                                       x-html="offer?.description || ''"></p>
                                </div>
                            </div>

                            <!-- Rewards -->
                            <div class="mt-4" x-show="offer?.events && offer?.events?.length > 0">
                                <p class="text-body fw-bold m-0 p-0 f-md" style="font-size: 14px; line-height: 160%">
                                    Rewards</p>
                                <template x-for="event in offer?.events" :key="event?.name">
                                    <div class="mt-1 px-4 p-2 bg-opacity-50 bg-dark text-truncate"
                                         style="border-radius: 20px; font-size: 11px">
                                    <span class="badge bg-label-primary text-center rounded"
                                          style="min-width: 60px; font-size: 11px">
                                        <template x-if="is_coin == '1'">
                                            <img src="{{ asset('assets/img/coin.png') }}" alt="" width="12px"
                                                 class="me-1">
                                        </template>

                                        <span
                                            x-text="is_coin == '1' ? Number(event?.points).toLocaleString() : '$' + event?.points.toFixed(2)"></span>
                                    </span>
                                        <span class="text-body ms-1" x-text="event?.name || ''"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- Steps -->
                            <div class="mt-4" x-show="offer?.instructions && offer?.instructions.length > 0">
                                <p class="fw-bold m-0 p-0 f-md" style="font-size: 14px; line-height: 160%">Steps</p>
                                <template x-for="(instruction, index) in offer?.instructions" :key="index">
                                    <div class="mt-2 d-flex align-items-center">
                                <span class="bg-label-secondary text-center rounded fw-medium"
                                      style="font-size: 11px !important ; width: 18px"
                                      x-text="index + 1"></span>
                                        <span class="small text-body ms-2" style="font-size: 11px"
                                              x-text="instruction"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
