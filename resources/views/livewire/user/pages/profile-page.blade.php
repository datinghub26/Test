<div>
    <div class="row g-4">
        <!-- LEFT COLUMN: Account Information, Level & Actions (col-12 col-lg-4) -->
        <div class="col-12 col-lg-4">
            <div class="card bg-body border-0 shadow-sm mb-4">
                <div class="card-header pb-3 d-flex align-items-center justify-content-between border-bottom border-secondary border-opacity-25">
                    <h5 class="card-title fw-bold text-white mb-0">
                        <i class="fa-solid fa-user text-primary me-2"></i> Account Information
                    </h5>
                    <button class="btn btn-sm btn-primary"
                            data-bs-target="#editProfileModal"
                            data-bs-toggle="modal">
                        <i class="fa-solid fa-pencil me-1"></i> Edit
                    </button>
                </div>
                <div class="card-body pt-4">
                    <div class="d-flex flex-column align-items-center text-center">
                        @php
                            $progress = auth()->user()->levelProgress() ?? 0;
                        @endphp
                        <div class="progress-circle mb-3 position-relative"
                             style="background: conic-gradient(var(--bs-primary) 0% {{ $progress }}%, var(--bs-body-bg) {{ $progress }}% 100%);">
                            <img src="{{ auth()->user()->avatar() }}" alt="User Avatar"
                                 class="rounded-circle"
                                 style="width: 96px; height: 96px; object-fit: cover;"
                                 onerror="this.onerror=null;this.src='{{ asset('assets/avatars/memoji_1.png') }}';">
                        </div>
                        <span class="badge bg-label-primary mb-2">Level {{ auth()->user()->level }}</span>
                        <h4 class="text-white fw-bold mb-1">{{ auth()->user()->username }}</h4>
                        <p class="text-secondary small mb-3">
                            {{ auth()->user()->email }}
                            @if(auth()->user()->email_verified_at)
                                <i class="fa-solid fa-check-circle text-primary ms-1" title="Verified"></i>
                            @else
                                <i class="fa-solid fa-exclamation-circle text-danger ms-1" title="Unverified"></i>
                            @endif
                        </p>
                    </div>

                    <!-- Level Experience Progress Box -->
                    <div class="p-3 bg-dark bg-opacity-50 rounded-3 mb-3">
                        <div class="d-flex justify-content-between small text-secondary mb-1">
                            <span>Level {{ auth()->user()->level }}</span>
                            <span class="text-white fw-semibold">{{ round($progress) }}%</span>
                            <span>Level {{ auth()->user()->level + 1 }}</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2" style="font-size: 11px;">
                            <span class="text-secondary">Current EXP:</span>
                            <span class="text-white fw-semibold">{{ number_format(auth()->user()->exp) }}</span>
                        </div>
                    </div>

                    <!-- Details List -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-secondary border-opacity-25 small">
                        <span class="text-secondary">Member Since</span>
                        <span class="text-white fw-medium">{{ auth()->user()->created_at->format('M Y') }} ({{ auth()->user()->created_at->diffForHumans() }})</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center py-2 border-top border-secondary border-opacity-25 small">
                        <span class="text-secondary d-flex align-items-center">
                            Private Profile
                            <i class="fa-solid fa-question-circle text-secondary ms-1"
                               data-bs-toggle="tooltip"
                               data-bs-placement="top"
                               title="Private mode hides your activity from other users"></i>
                        </span>
                        <label class="switch switch-primary f-md mb-0">
                            <input type="checkbox" class="switch-input" required="" wire:model="private"
                                   wire:click="togglePrivacy">
                            <span class="switch-toggle-slider" style="top: 0">
                                <span class="switch-on"></span>
                                <span class="switch-off bg-secondary"></span>
                            </span>
                        </label>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('referrals') }}" class="btn btn-outline-primary w-100 btn-sm">
                            <i class="fa-solid fa-handshake-angle me-2"></i> Referral Program
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN: Stats & Activity Tabs (col-12 col-lg-8) -->
        <div class="col-12 col-lg-8">
            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-sm-3">
                    <div class="card bg-body border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="badge rounded-pill p-2 bg-label-primary mb-2">
                                <i class="fa-solid fa-circle-check" style="font-size: 18px"></i>
                            </div>
                            <h5 class="card-title mb-1 text-white f-md">{{ $leadsCount }}</h5>
                            <small class="text-secondary" style="font-size: 11px;">Completed</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="card bg-body border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="badge rounded-pill p-2 bg-label-info mb-2">
                                <i class="fa-solid fa-users" style="font-size: 18px"></i>
                            </div>
                            <h5 class="card-title mb-1 text-white f-md">{{ $referralsCount }}</h5>
                            <small class="text-secondary" style="font-size: 11px;">Referred</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="card bg-body border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="badge rounded-pill p-2 bg-label-success mb-2">
                                <i class="fa-solid fa-wallet" style="font-size: 18px"></i>
                            </div>
                            <h5 class="card-title mb-1 text-white f-md">{{ number_format($leadsPoints) }}</h5>
                            <small class="text-secondary" style="font-size: 11px;">Total ERC</small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="card bg-body border-0 shadow-sm h-100">
                        <div class="card-body text-center p-3">
                            <div class="badge rounded-pill p-2 bg-label-warning mb-2">
                                <i class="fa-solid fa-clock" style="font-size: 18px"></i>
                            </div>
                            <h5 class="card-title mb-1 text-white f-md">{{ number_format($lastMonthLeadsPoints) }}</h5>
                            <small class="text-secondary" style="font-size: 11px;">Last 30 Days</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-body p-0 border-0 shadow-sm">
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
                                        <th>Status</th>
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
                                            <td>
                                                @if($lead->status === 'approved')
                                                    <span class="badge bg-label-success">Approved</span>
                                                @elseif($lead->status === 'pending')
                                                    <span class="badge bg-label-warning">Pending</span>
                                                @else
                                                    <span class="badge bg-label-danger">{{ ucfirst($lead->status) }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $lead->created_at->diffForHumans() }}</td>
                                            <td class="text-truncate">
                                                <x-coins :coins="$lead->points"/>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No activity found</td>
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




