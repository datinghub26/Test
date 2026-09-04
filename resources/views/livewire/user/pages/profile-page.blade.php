<div>
    <div class="card ">
        <div class="card-body p-3">
            <div class="card bg-body">
                <div class="card-header pb-4">
                    <h5 class="card-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-user  text-secondary me-2"></i>
                        Account Information
                        <a class="btn btn-sm btn-primary float-end"
                           data-bs-target="#editProfileModal"
                           data-bs-toggle="modal">
                            <i class="fa-solid fa-pencil me-2"></i>
                            Edit
                        </a>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between gap-2 pt-2">
                        <div class="d-flex flex-column justify-content-start align-items-center">
                            @php
                                $progress = auth()->user()->levelProgress() ?? 0;
                            @endphp
                            <div class="progress-circle"
                                 style="background: conic-gradient(var(--bs-primary) 0% {{ $progress }}%, var(--bs-body-bg) {{ $progress }}% 100%);">
                                <img src="{{ auth()->user()->avatar() }}" alt="User Avatar"
                                     class="rounded-circle"
                                     style="width: 100px;"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/avatars/memoji_1.png') }}';">
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-label-primary">Level {{ auth()->user()->level }}</span>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center flex-grow-1 ms-0 ms-4">
                            <small class="text-body">Joined {{ auth()->user()->created_at->diffForHumans() }}</small>
                            <h5 class="text-white fw-bold mb-0 h1">{{ auth()->user()->username }}</h5>
                            <p class="text-body small"> {{ auth()->user()->email }}
                                @if(auth()->user()->email_verified_at)
                                    <i class="fa-solid fa-check-circle text-primary"></i>
                                @else
                                    <i class="fa-solid fa-exclamation-circle text-danger"></i>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mt-4 m-auto">
                        <div class="col-6">
                            <div class="d-flex align-items-center m-auto">
                                <svg width="24" height="24" viewBox="0 0 28 22">
                                    <path fill="currentColor"
                                          d="M14 1.7c-.4.02-1.4-.07-2.18-.6-.98-.68-2.64-.98-3.62.22-.95 1.16-1.83 4.74-2.15 6.6C2.39 8.43 0 9.31 0 10.31c0 1.62 6.27 2.93 14 2.93s14-1.31 14-2.93c0-1-2.4-1.88-6.05-2.4-.32-1.86-1.2-5.44-2.15-6.6-.98-1.2-2.64-.9-3.62-.23-.78.54-1.78.63-2.18.6Z"></path>
                                    <path fill="currentColor" fill-rule="evenodd"
                                          d="M5.34 13.85c1.23.2 4.69.6 8.66.6 3.97 0 7.43-.4 8.66-.6v3.37c0 1.32-.96 2.6-1.43 3.08-.4.4-1.55 1.2-2.94 1.2-1.73 0-2.63-.6-3.09-1.05A1.87 1.87 0 0 0 14 20c-.25 0-.84.09-1.2.45-.46.45-1.36 1.05-3.09 1.05-1.39 0-2.53-.8-2.94-1.2a4.94 4.94 0 0 1-1.43-3.08v-3.37ZM7.57 17c.2-.69 1.08-1.2 2.14-1.2s1.94.51 2.14 1.2c-.2.68-1.08 1.2-2.14 1.2s-1.94-.52-2.14-1.2Zm8.54 0c.2-.69 1.08-1.2 2.14-1.2 1.05 0 1.93.51 2.14 1.2-.2.68-1.09 1.2-2.14 1.2-1.06 0-1.94-.52-2.14-1.2Z"
                                          clip-rule="evenodd"></path>
                                </svg>

                                <span class="text-white ms-2 me-2">
                                    Private
                                    <i class="fa-solid fa-question-circle text-secondary"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-bs-original-title="Private mode hides your activity from other users"></i>
                                </span>
                                <label class="switch switch-primary f-md">
                                    <input type="checkbox" class="switch-input" required="" wire:model="private"
                                           wire:click="togglePrivacy">
                                    <span class="switch-toggle-slider" style="top: 0">
                                        <span class="switch-on"></span>
                                        <span class="switch-off bg-secondary"></span>
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-body mt-3">
                <div class="card-header pb-0">
                    <h5 class="card-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-chart-simple text-secondary me-2"></i>
                        Stats
                    </h5>
                </div>

                <div class="card-body mt-3 pb-0 ">
                    <div class="row mt-4">
                        <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                                        <i class="fa-solid fa-circle-check" style="font-size: 20px"></i>
                                    </div>
                                    <h5 class="card-title mb-2 text-white f-md">{{ $leadsCount }}</h5>
                                    <small class="text-body h6">Completed Offers</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                            <div class="card h-100 ">
                                <div class="card-body text-center">
                                    <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                                        <i class="fa-solid fa-users" style="font-size: 20px"></i>
                                    </div>
                                    <h5 class="card-title mb-2 text-white f-md">{{ $referralsCount }}</h5>
                                    <small class="text-body h6">Users Referred</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                            <div class="card h-100 ">
                                <div class="card-body text-center">
                                    <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                                        <i class="fa-solid fa-wallet" style="font-size: 20px"></i>
                                    </div>
                                    <h5 class="card-title mb-2 text-white f-md">{{ $leadsPoints }}</h5>
                                    <small class="text-body h6">Total Earning</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-2 col-lg-4 col-md-4 col-6 mb-4">
                            <div class="card h-100 ">
                                <div class="card-body text-center">
                                    <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                                        <i class="fa-solid fa-clock" style="font-size: 20px"></i>
                                    </div>
                                    <h5 class="card-title mb-2 text-white f-md">{{ $lastMonthLeadsPoints }}</h5>
                                    <small class="text-body h6">Earnings last 30 days</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-body p-0 mt-3">
                <div class="card-header pb-0">
                    <ul class="nav nav-pills mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button @class(['nav-link d-flex align-items-center', 'active' => $tab == 1])
                                    type="button"
                                    wire:click="$set('tab', 1)"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-earnings">
                                <i class="fa-solid fa-sack-dollar me-2"></i> Earnings
                                <span
                                    class="badge rounded-pill badge-center bg-label-primary ms-2">{{ $leadsCount }}</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button @class(['nav-link d-flex align-items-center', 'active' => $tab == 2])
                                    type="button"
                                    wire:click="$set('tab', 2)"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-withdrawals">
                                <i class="fa-solid fa-share me-2"></i> Withdrawals
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button @class(['nav-link d-flex align-items-center', 'active' => $tab == 3])
                                    type="button"
                                    wire:click="$set('tab', 3)"
                                    role="tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#tab-pending">
                                <i class="fa-solid fa-hourglass-end me-2"></i> Pending
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-0">
                    <div class="tab-content">
                        <div @class(['tab-pane fade', 'show active' => $tab == 1]) id="tab-earnings" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 small " style="font-weight: 600">
                                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Time</th>
                                        <th>ERC</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($leads as $lead)
                                        <tr>
                                            <td class="d-flex align-items-center gap-2">
                                                @if($lead->image)
                                                    <img src="{{ $lead->image }}" alt="{{ $lead->name }}"
                                                         class="rounded-3" style="width: 30px;"
                                                         onerror="this.onerror=null;this.src='{{ asset('assets/img/placeholder-offer.svg') }}';">
                                                @else
                                                    <div
                                                        class="bg-label-primary bg-opacity-50 rounded-3  d-flex align-items-center"
                                                        style="width: 30px; height: 30px;">
                                                        <i class="fa-solid fa-rocket text-primary m-auto mt-2"
                                                           style="font-size: 18px;"></i>
                                                    </div>
                                                @endif
                                                <span class="text-truncate">{{ $lead->name }}</span>
                                            </td>
                                            <td>{{ $lead->created_at->diffForHumans() }}</td>
                                            <td class="text-truncate">
                                                <x-coins :coins="$lead->points"/>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No activity found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $leads->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                        <div @class(['tab-pane fade', 'show active' => $tab == 2]) id="tab-withdrawals" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 small " style="font-weight: 600">
                                    <thead>
                                     <tr>
                                        <th>Method</th>
                                        <th>Time</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($withdrawals as $withdrawal)
                                        <tr>
                                            <td class="d-flex align-items-center gap-2">
                                                <img src="{{ Storage::url($withdrawal->method_image) }}"
                                                     alt="{{ $withdrawal->method_name }}"
                                                     style="width: 30px;"
                                                     onerror="this.onerror=null;this.src='{{ asset('assets/img/placeholder-provider.svg') }}';">
                                            </td>
                                            <td>{{ $withdrawal->updated_at->diffForHumans() }}</td>
                                            <td class="text-truncate">
                                                <x-coins :coins="$withdrawal->amount"/>
                                            </td>
                                        </tr>

                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No withdrawals found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $withdrawals->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                        <div @class(['tab-pane fade', 'show active' => $tab == 3]) id="tab-pending" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0 small " style="font-weight: 600">
                                    <thead>
                                    <tr>
                                        <th>Method</th>
                                        <th>Time</th>
                                        <th>Amount</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($pendingWithdrawals as $withdrawal)
                                        <tr>
                                            <td class="d-flex align-items-center gap-2">
                                                <img src="{{ Storage::url($withdrawal->method_image) }}"
                                                     alt="{{ $withdrawal->method_name }}"
                                                     style="width: 30px;"
                                                     onerror="this.onerror=null;this.src='{{ asset('assets/img/placeholder-provider.svg') }}';">
                                            </td>
                                            <td>{{ $withdrawal->updated_at->diffForHumans() }}</td>
                                            <td class="text-truncate">
                                                <x-coins :coins="$withdrawal->amount"/>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No withdrawals found</td>
                                        </tr>
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-4">
                                {{ $pendingWithdrawals->links(data: ['scrollTo' => false]) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div wire:ignore.self class="modal fade" id="editProfileModal" tabindex="-1"
         aria-labelledby="editProfileModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-body f-md" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="username" class="form-label text-body">Verify your email</label>
                        @if(auth()->user()->hasVerifiedEmail())
                            <div class="alert alert-success small">
                                <i class="fa-solid fa-check-circle me-2 "></i>
                                Your email is verified
                            </div>
                        @else
                            <button class="btn btn-secondary w-100"
                                    wire:click="sendEmailVerificationLink()" wire:loading.attr="disabled">
                                Resend verification link
                            </button>
                        @endif
                    </div>

                    <hr class="my-4">


                    <form wire:submit.prevent="updateProfile()">
                        <div class="mb-3">
                            <label for="username" class="form-label text-body">Username</label>
                            <input @class(['form-control', 'is-invalid' => $errors->has('username')])
                                   type="text" id="username" wire:model="username"
                                   placeholder="Enter your username">

                            @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label text-body">Email</label>
                            <input @class(['form-control', 'is-invalid' => $errors->has('email')])
                                   type="email" id="email" wire:model="email"
                                   placeholder="Enter your email">

                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100 ">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>




