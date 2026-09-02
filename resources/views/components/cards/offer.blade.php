@props(['offer', 'swiper' => false])

<div
    {{ $attributes->merge(['class' => 'fade-in-scale mb-3 d-inline-block float-none'. ($swiper ? ' swiper-offer-card' : '') ]) }}
    x-data="{ offer: @js($offer) }"
    @auth x-on:click="$dispatch('open-offer-api-modal', offer)"
    @else x-on:click="$('#authModal').modal('show')" @endauth
>
    <a class="card bg-dark bg-opacity-100 offer-card p-2"
       style="cursor: pointer; border: none; background: rgba(var(--bs-dark-rgb), var(--bs-bg-opacity)) !important;">
        <div class="card-img-block text-center align-items-center d-flex justify-content-center">
            <img class="card-img-top" style="border-radius: 10px"
                 src="{{ $offer['image'] }}"
                 alt="{{ $offer['title'] }}"
                 onerror="this.src='{{ asset('assets/img/Image-not-found.png') }}'"
                 @if(!$swiper) style="min-height: 124px;" @endif
            >

            <div class="badge bg-label-dark position-absolute"
                 style="top: 10px; right: 10px; padding: 5px 8px; background-color: rgba(7,8,9,0.56) !important">
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <span class="fa-brands fa-android text-white"
                          x-show="{{ (int)in_array('Android', $offer['devices'])  }}"></span>
                    <span class="fa-brands fa-apple text-white"
                          x-show="{{ (int)in_array('iOS', $offer['devices']) }}"></span>
                    <span class="fa-solid fa-laptop text-white"
                          x-show="{{ (int)in_array('Desktop', $offer['devices']) || count($offer['devices'] ?? []) == 0 ? 1 : 0  }}"></span>
                </div>
            </div>

            <i class="fa-solid fa-play position-absolute text-white text-primary rounded-circle"
               style="background: rgba(var(--bs-primary-rgb), 0.30) !important"></i>
        </div>
        <div class="card-body p-0 pt-0 pb-3 small text-start">
            <span class="text-truncate text-white d-block mt-2">{{ html_entity_decode($offer['title']) }}</span>
            <span
                class="text-truncate text-secondary d-block">{{ $offer['requirements'] ?? $offer['description'] }}</span>
        </div>

        <div class="card-footer justify-content-between border-0 pt-0 pb-1 p-0 text-start">
            <div class="f-w-600 text-white">
                {{--                <span class="badge bg-gradient-dark">--}}
                @php
                    $price = to_money($offer['points']);
                    $price = number_format($price, 2);
                    $price = explode('.', $price);
                @endphp
                <span class="d-flex align-items-end fw-medium" style="font-size: 17px; line-height: 16px"
                      x-show.important="is_coin == '0'">
                        ${{ $price[0] }}.
                        <span style="font-size: 13px; line-height: 12px" class="fw-medium">{{ $price[1] }}</span>
                    </span>

                <span x-show.important="is_coin == '1'" class="d-flex align-items-center fw-medium">
                        <img src="{{ asset('assets/img/coin.png') }}" alt="" width="17px" class="me-1">
                        <span style="font-size: 15px">{{ format_coins($offer['points']) }}</span>
                </span>
                {{--                </span>--}}
            </div>
        </div>
    </a>
</div>


