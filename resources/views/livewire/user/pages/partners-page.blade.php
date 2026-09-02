<div>
    <div class="card">
        <div class="card-header">
            <div class="h5 card-title fw-bold text-white d-flex align-items-center gap-1">
                <x-heroicon-s-sparkles class="text-primary" width="25px"/>
                <span>Partners</span>
                <x-heroicon-c-question-mark-circle class="text-secondary" width="25px" data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="Complete offers to earn rewards"/>

            </div>

            {{--                <div class="d-flex align-items-center justify-content-between">--}}
            {{--                    <div class="">--}}
            {{--                        <i class="fa-solid fa-hat-cowboy text-primary"></i> Partners--}}
            {{--                        <i class="fa-solid fa-question-circle text-secondary" data-bs-toggle="tooltip"--}}
            {{--                           data-bs-placement="top"--}}
            {{--                           title="These are the partners that provide the offers"></i>--}}
            {{--                    </div>--}}
            {{--                </div>--}}
            {{--        </div>--}}
        </div>

        <div class="card-body">
            <div class="py-2 card-container">
                @php
                    $userLevel = auth()->check() ? auth()->user()->level : 0;
                @endphp
                @foreach($offers as $offer)
                    <x-cards.partner :offer="$offer" :is-locked="$offer->unlock_level > $userLevel"/>
                @endforeach
            </div>

            @if(count($offers) == 0)
                <x-empty-state message="No partners available at the moment"/>
            @endif
        </div>
    </div>
</div>
