<div>
    <div class="card">
        <div class="card-header pb-0">
            <div class="card-title h5 fw-bold text-white d-flex align-items-center gap-1">
                <x-heroicon-s-gift class="text-primary" class="text-primary " width="25px" />
                Rewards
            </div>
        </div>
        <div class="card-body mt-3 px-3"
             x-data="timer({{ $this->streakEndTime()?->timestamp }})"
             x-init="init()">
            <div class="card  @if(setting('streaks.enable_rewards', true)) bg-body @endif">
                @if(setting('streaks.enable_rewards', true))
                    <div class="card-header pb-0">
                        <h5 class="card-title fw-bold text-white">
                            {{ $streaks->count() }} Day Streak Rewards
                        </h5>

                        <span class="small text-secondary fw-normal">
                        <span class="text-primary">Earn <span
                                x-text="is_coin == '1' ? '{{ number_format(setting('streaks.required_points', 1000)) }} ERC' : '${{ to_money_str(setting('streaks.required_points', 1000)) }}' "></span> </span>or more within <span
                                class="text-primary">{{ setting('streaks.required_hours', 24) }} hours</span> to keep your streak
                    </span>
                    </div>
                    <div class="card-body mt-3">
                        <div class="streak-container">
                            @foreach($streaks as $streak)
                                <div class="streak-card card">
                                    <div class="card-body">
                                        <i class="fa-regular fa-clock text-body mb-2"></i>
                                        <h5 class="text-white mb-1">Day {{ $streak->day }}</h5>
                                        <p>{{ number_format($streak->points) }} ERC</p>
                                        <button
                                            @class(['btn btn-secondary btn-sm py-1 px-2',
                                                    'disabled' => !$this->isUserCanClaimStreak($streak->id),
                                                    'btn-label-danger' => $this->isStreakEnded()
                                                    ])
                                            wire:click="claimStreak({{ $streak->id }})"
                                            @if($this->isUserCanClaimStreak($streak->id))
                                                wire:loading.attr="disabled"
                                            @endif
                                        >
                                            @if($this->isStreakEnded())
                                                Streak Ended
                                            @elseif($this->isUserClaimedStreak($streak->id))
                                                Claimed
                                            @else
                                                Claim
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="alert alert-secondary d-flex align-items-center mt-3" role="alert">
                            <i class="fa-solid fa-info-circle me-2" style="font-size: 15px"></i>


                            <span class="small text-body fw-normal ">
                            Earn <span
                                    class="text-primary fw-bold"> {{ number_format(setting('streaks.required_points', 1000)) }} more ERC today</span> to keep your streak! Time left
                            <span class="text-primary fw-bold" @if(!$this->isUserFirstStreak()) x-text="timeLeft" @endif>
                                24h
                            </span>

                        </span>
                        </div>
                    </div>
                @else
                    <div class="row text-center text-secondary p-4">
                        <x-heroicon-s-archive-box-x-mark style="width: 75px; height: 75px; margin: auto"/>
                        <span class="text-center">No rewards available at the moment.</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
