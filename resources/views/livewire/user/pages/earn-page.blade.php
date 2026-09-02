<div>
    
    <div class="card new-carder-card">
        <div class="card-header mb-0 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="h5 card-title fw-bold text-white d-flex align-items-center gap-1">
                    <i class="fa-solid fa-dumpster-fire text-primary" width="25px"></i>
                    <span>Top Offers</span>
                </div>

                <a class="btn btn-sm btn-secondary" href="{{ route('offers') . '?category=Games' }}">View All</a>
            </div>

        </div>

        <div class="card-body mb-0 pb-0">
            <livewire:user.offers :swiper="true" :limit="20"  :sort="4" lazy/>
        </div>
    </div>
    
    
    
    <div class="card mt-4 new-carder-card">
        <div class="card-header mb-0 pb-0">
            <div class="d-flex align-items-center justify-content-between">
                <div class="h5 card-title fw-bold text-white d-flex align-items-center gap-1">
                    <i class="fa-solid fa-jet-fighter-up text-primary" width="25px"></i>
                    <span>Featured Offers</span>
                </div>

                <a class="btn btn-sm btn-secondary" href="{{ route('offers') }}">View All</a>
            </div>

        </div>

        <div class="card-body mb-0 pb-0">
            <livewire:user.offers :swiper="true" :limit="20" :sort="3" lazy/>
        </div>
    </div>
    
    
    
    
    
    
    <div class="card mt-4 new-carder-card">
        <div class="card-header mb-0 pb-0">
            <h5 class="card-title fw-bold text-white ">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="">
                        <x-heroicon-s-sparkles class="text-primary" width="25px"/>
                        Offers Partners
                        <x-heroicon-s-question-mark-circle class="text-secondary" width="25px" data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="Complete offers to earn rewards"/>
                    </div>

                    <div class="">
                        <a class="btn btn-sm btn-secondary" href="{{ route('partners') }}">View All</a>
                    </div>
                </div>
            </h5>
        </div>
        <div class="card-body mb-0 pb-0">
            <div class="d-flex flex-row flex-nowrap overflow-auto ">
                <div class="swiper py-3" x-init="new window.swiper($el, { slidesPerView: 'auto' })">
                    <div class="swiper-wrapper" wire:ignore.self>
                        @foreach($offerPartners as $offer)
                            <x-cards.partner :offer="$offer" class="swiper-slide d-flex mx-2" :is-locked="$offer->unlock_level > $userLevel"/>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(count($offerPartners) == 0)
                <x-empty-state message="No partners available at the moment"/>
            @endif
        </div>
    </div>
    <div @class(['card mt-4 new-carder-card', 'd-none' => count($surveyPartners) == 0])>
        <div class="card-header mb-0 pb-0">
            <h5 class="card-title fw-bold text-white ">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="">
                        <x-heroicon-s-pencil-square class="text-primary" width="25px"/>
                        Survey Partners
                        <x-heroicon-s-question-mark-circle class="text-secondary" width="25px" data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="Complete surveys to earn rewards"/>
                    </div>

                    <div class="">
                        <a class="btn btn-sm btn-secondary" href="{{ route('surveys') }}">View All</a>
                    </div>
                </div>

            </h5>
        </div>
        <div class="card-body mb-0 pb-0">
            <div class="d-flex flex-row flex-nowrap overflow-auto ">
                <div class="swiper py-3" x-init="new window.swiper($el, { slidesPerView: 'auto' })">
                    <div class="swiper-wrapper" wire:ignore.self>
                        @foreach($surveyPartners as $survey)
                            <x-cards.partner :offer="$survey" class="swiper-slide d-flex mx-2" :is-locked="$survey->unlock_level > $userLevel"/>
                        @endforeach
                    </div>
                </div>
            </div>
            @if(count($surveyPartners) == 0)
                <x-empty-state message="No partners available at the moment"/>
            @endif
        </div>
    </div>
</div>

