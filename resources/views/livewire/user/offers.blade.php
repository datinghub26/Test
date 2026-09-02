<div>
    <div @class(['swiper' => $swiper, 'mt-4']) wire:loading.flex>
        <div @class(['offer-api-card mt-4' => $swiper == false, 'swiper-wrapper swiper-offer d-flex gap-2' => $swiper])>
            @foreach(range(1, $this->limit ?? 40) as $i)
                <div @class(['mb-3 d-inline-block float-none', 'col-4 col-xxl-3 col-xl-3 col-lg-3 col-md-3 swiper-offer-card' => $swiper])>
                    <div @class(['card bg-dark bg-opacity-75 p-2 skeleton-card'])>
                        <div class="card-img-block text-center skeleton skeleton-img"></div>
                        <div class="skeleton skeleton-text mt-2"></div>
                        <div class="skeleton skeleton-text"></div>
                        <div class="skeleton skeleton-footer"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        @if($offers?->count() <= 0)
            <x-empty-state message="No offers available at the moment"/>
        @endif

        <div
            @class(['swiper' => $swiper, 'd-none' => $offers?->count() == 0]) x-init="if($refs.swiper) { window.swiper($refs.swiper, { slidesPerView: 'auto' }) }"
            @if($swiper) x-ref="swiper" @endif>
            <div
                @class(['offer-api-card mt-4' => $swiper == false, 'swiper-wrapper swiper-offer d-flex gap-2' => $swiper])
                wire:ignore.self
                wire:loading.remove>
                @foreach($offers as $offer)
                    <x-cards.offer @class(['swiper-slide col-4 col-xxl-3 col-xl-3 col-lg-3 col-md-3' => $swiper])
                                   wire:key="{{ rand() }}"
                                   :swiper="$swiper"
                                   :offer="$offer"
                    />
                @endforeach
            </div>

            @if($offers instanceof \Illuminate\Pagination\LengthAwarePaginator && $swiper === false)
                <div class="d-flex justify-content-center w-100 mt-4">
                    {{ $offers->links(data: ['scrollTo' => true]) }}
                </div>
            @endif
        </div>
    </div>
</div>
