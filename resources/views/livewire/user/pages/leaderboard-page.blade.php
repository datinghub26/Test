<div>
    <div class="row d-flex justify-content-center align-items-end gap-1 gap-md-3 leaderboard-container">
        @if($leaders[2] ?? false)
            <x-cards.leaderboard :leader="$leaders[2]" rank="3" :prize="$this->getPrize(3)"/>
        @endif
        @if($leaders[0] ?? false)
            <x-cards.leaderboard :leader="$leaders[0]" rank="1" :prize="$this->getPrize(1)"/>
        @endif
        @if($leaders[1] ?? false)
            <x-cards.leaderboard :leader="$leaders[1]" rank="2" :prize="$this->getPrize(2)"/>
        @endif
    </div>


    <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-2">
        <div class="text-center">
            <span class="badge bg-dark m-auto text-center align-items-center d-flex gap-1" style="width: fit-content">
                <x-heroicon-s-credit-card class="text-primary" width="20px"/>
                You earned
                <x-coins :coins="auth()->user()?->todayPoints() ?? 0" size="12px"/>
                today
            </span>
        </div>

        <div class="text-center">
            <span class="badge bg-dark m-auto text-center align-items-center d-flex gap-1" style="width: fit-content"
                  x-data="timer({{ $end->timestamp }})"
                  x-init="init()">
                <x-heroicon-s-clock class="text-primary me-2" width="20px"/>Ends in <span class=""
                                                                                          x-text="timeLeft"></span>
            </span>
        </div>
    </div>


    <div class="row justify-content-center mt-5">
        <div class="col">
            <div class="table-responsive">
                <table class="table align-middle mb-0 small" style="font-weight: 600">
                    <thead>
                    <tr>
                        <th>Rank</th>
                        <th>User</th>
                        <th>Points</th>
                        <th>Prize</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($leaders as $leader)
                        <tr>
                            <td>
                                @if($loop->iteration == 1)
                                    <img src="{{ asset('assets/img/leaderboard/first.png') }}" alt="First Place"
                                         style="width: 20px">
                                @elseif($loop->iteration == 2)
                                    <img src="{{ asset('assets/img/leaderboard/second.png') }}" alt="Second Place"
                                         style="width: 20px">
                                @elseif($loop->iteration == 3)
                                    <img src="{{ asset('assets/img/leaderboard/third.png') }}" alt="Third Place"
                                         style="width: 20px">
                                @else
                                    <span class="badge bg-label-primary m-0 p-2">{{ $loop->iteration }}</span>
                                @endif
                            </td>
                            <td class="text-truncate">
                                <a href="#"
                                   @click="$dispatch('activity-open', {user_id: '{{ $leader->user->id }}'})">
                                    <img src="{{ $leader->user->avatar() }}"
                                         alt="User Avatar"
                                         class="me-1" style="width: 24px">
                                    <span class="text-white">{{ $leader->user->username }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-secondary"
                                       style="font-size: 12px"></i>
                                </a>
                            </td>
                            <td class="text-primary">{{ $leader->points }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img x-show="is_coin == '1'"
                                         src="{{ asset('assets/img/coin.png') }}"
                                         alt="" width="10px" class="me-1">

                                    <span
                                        x-text="is_coin == '1' ? '{{ number_format($this->getPrize($loop->iteration)) }}' : '${{ to_money_str($this->getPrize($loop->iteration)) }}'"></span>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>


            </div>

        </div>
    </div>

</div>


