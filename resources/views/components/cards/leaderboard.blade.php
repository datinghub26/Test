@props(['leader', 'rank', 'prize'])

<div @class([
        'col-auto leaderboard-card' => true,
        'first-place' => $rank == 1,
        'sec-place' => $rank == 2,
        'third-place' => $rank == 3
    ])
>
    <div class="position-absolute place" style="right: 10px; top: 15px">
        @if($rank == 1)
            <img src="{{ asset('assets/img/leaderboard/first.png') }}" alt="">
        @elseif($rank == 2)
            <img src="{{ asset('assets/img/leaderboard/second.png') }}" alt="">
        @elseif($rank == 3)
            <img src="{{ asset('assets/img/leaderboard/third.png') }}" alt="">
        @endif
    </div>

    <div class="leaderboard-image position-relative">
        <img src="{{ $leader->user->avatar() }}" alt="{{ $leader->user->username }}">
        @if($rank == 1)
            <img class="crown" src="{{ asset('assets/img/crown.webp') }}" alt="Crown">
        @endif
    </div>

    <p class="mt-3 mb-0 f-md text-break">{{ $leader->user->username }}</p>
    <p class="text-primary mt-1 f-md">{{$leader->points }}</p>
    <span class="badge bg-dark align-items-center justify-content-between px-2 d-none d-md-flex">
        <span @class(['badge bg-label-warning' => $rank == 1,'badge bg-label-secondary' => $rank == 2,'badge bg-label-primary' => $rank == 3])>
            {{ $rank }}
        </span>
        <span class="text-body">Prize</span>
        <x-coins :coins="$prize"/>
    </span>
</div>
