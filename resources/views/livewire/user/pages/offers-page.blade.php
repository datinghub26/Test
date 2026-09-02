<div>
    <div class="card">
        <div class="card-header">
            <div class="h5 card-title fw-bold text-white d-flex align-items-center gap-1">
                <x-heroicon-s-rocket-launch class="text-primary" width="25px"/>
                <span>Offers</span>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-auto col-12">
                    <div class="input-group search-input-group">
                        <input type="text" class="form-control search-input " placeholder="Search"
                               wire:model.live="search">
                        <div class="input-group-prepend">
                            <span class="input-group-text search-input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-auto mt-4 mt-md-0">
                    <div class="dropdown d-flex align-items-center" style="font-weight: 350; font-size: 14px">
                        <span class="text-secondary small me-2 d-none d-md-block">Sort by:</span>
                        <button class="btn bg-dark dropdown-toggle" type="button"
                                style="font-weight: 350; font-size: 14px"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            @if($sort == 1)
                                <i class="fa-solid fa-arrow-up me-2"></i> Highest Reward
                            @elseif($sort == 2)
                                <i class="fa-solid fa-arrow-down me-2"></i> Lowest Reward
                            @elseif($sort == 3)
                                <i class="fa-regular fa-star me-2"></i> Most Popular
                            @endif
                        </button>
                        <ul class="dropdown-menu bg-dark text-start" aria-labelledby="dropdownMenuButton1"
                            style="cursor: pointer; font-weight: 350; font-size: 14px">
                            <li>
                                <a @class(["dropdown-item", "active" => $sort == 1]) wire:click="$set('sort', 1)">
                                    <i class="fa-solid fa-arrow-up me-2"></i> Highest Reward</a>
                            </li>
                            <li>
                                <a @class(["dropdown-item", "active" => $sort == 2]) wire:click="$set('sort', 2)">
                                    <i class="fa-solid fa-arrow-down me-2"></i> Lowest Reward</a>
                            </li>
                            <li>
                                <a @class(["dropdown-item", "active" => $sort == 3]) wire:click="$set('sort', 3)">
                                    <i class="fa-regular fa-star me-2"></i> Most Popular</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-auto mt-4 mt-md-0">
                    <div class="dropdown d-flex align-items-center" style="font-weight: 350; font-size: 14px">
                        <button class="btn bg-dark dropdown-toggle"
                                style="font-weight: 350; font-size: 14px"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(empty($this->provider))
                                <i class="fa-solid fa-hand-holding-dollar me-2 "></i> All Providers
                            @else
                                <i class="fa-solid fa-hand-holding-dollar me-2"></i> {{ $this->provider }}
                            @endif
                        </button>

                        <ul class="dropdown-menu bg-dark text-start"
                            style="cursor: pointer; font-weight: 350; font-size: 14px">
                            <li>
                                <a class="dropdown-item" wire:click="$set('provider', '')">All</a>
                            </li>
                            @foreach($providers as $provider)
                                <li>
                                    <a @class(["dropdown-item", "active" => $this->provider == $provider]) wire:click="$set('provider', '{{ $provider }}')">
                                        {{ $provider }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="col-auto mt-4 mt-md-0">
                    <div class="dropdown d-flex align-items-center" style="font-weight: 350; font-size: 14px"
                         wire:ignore.self>
                        <button class="btn bg-dark dropdown-toggle"
                                data-bs-auto-close="outside"
                                style="font-weight: 350; font-size: 14px"
                                type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @if(!count($devices))
                                <i class="fa-solid fa-laptop-file me-2 "></i> All Devices
                            @else
                                <i class="fa-solid fa-laptop-file me-2 "></i>
                                @foreach($devices as $device)
                                    @if($loop->last)
                                        {{ ucfirst($device) }}
                                    @else
                                        {{ ucfirst($device) }},
                                    @endif
                                @endforeach
                            @endif
                        </button>

                        <ul class="dropdown-menu bg-dark text-start"
                            style="cursor: pointer; font-weight: 350; font-size: 14px">
                            <li>
                                <a class="dropdown-item" wire:click="$set('devices', [])">
                                    <i class="fa-solid fa-laptop-file me-2 "></i>All
                                </a>
                            </li>

                            <li>
                                <a @class(["dropdown-item", "active" => in_array('Desktop', $devices)]) wire:click="pushDevice('Desktop')">
                                    <i class="fa-solid fa-laptop me-2"></i> Desktop
                                </a>
                            </li>
                            <li>
                                <a @class(["dropdown-item", "active" => in_array('Android', $devices)]) wire:click="pushDevice('Android')">
                                    <i class="fa-brands fa-android me-2"></i> Android
                                </a>
                            </li>

                            <li>
                                <a @class(["dropdown-item", "active" => in_array('iOS', $devices)]) wire:click="pushDevice('iOS')">
                                    <i class="fa-brands fa-apple me-2"></i> iOS
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>

            <livewire:user.offers :sort="$sort" :search="$search" :provider="$this->provider" :devices="$devices"
                                  :category="$category"/>
        </div>
    </div>
</div>
