@assets
<style>
    .lead-name {
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
        width: 150px;
    }

    @media (max-width: 768px) {
        .lead-name {
            width: 100px;
        }
    }
</style>
@endassets

<div>
    <div
        wire:ignore.self
        class="modal fade"
        id="activityModal"
        tabindex="-1"
        aria-labelledby="activityModal"
        aria-hidden="true"
        x-data="{ open: @entangle('show') }"
        x-init="() => {
                $watch('open', value => {
                    if (value) {
                        $('#activityModal').modal('show');
                    } else {
                        $('#activityModal').modal('hide');
                    }
                });

                $('#activityModal').on('hidden.bs.modal', function () {
                    @this.closeModal();
                });
            }"
        style="z-index: 9999999999;">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-sm-down">
            <div class="modal-content">
                <div class="modal-header mb-0 pb-0">
                    <div class="row align-items-center">
                        <div class="col-4 col-md-6 text-center">
                            @php
                                $progress = $user?->levelProgress() ?? 0;
                            @endphp

                            <div class="progress-circle"
                                 style="background: conic-gradient(var(--bs-primary) 0% {{ $progress }}%, var(--bs-dark) {{ $progress }}% 100%);">
                                <img src="{{ $user?->avatar() }}" alt="{{ $user?->username }}"
                                     class="rounded-circle"
                                     style="width: 100px;"
                                     onerror="this.onerror=null;this.src='{{ asset('assets/avatars/memoji_1.png') }}';">
                            </div>
                            <div class="mt-2">
                                <span class="badge bg-primary">Level {{ $user?->level }}</span>
                            </div>
                        </div>
                        <div class="col-8 col-md-6 text-center">
                            <h5 class="fw-bold mb-0 h2">{{ $user?->username }}</h5>
                            <small class="text-secondary">{{ $user?->created_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <hr>

                    <div class="row text-center">
                        <h5 class="fw-bold text-start text-white">
                            <i class="fa-solid fa-chart-simple text-secondary me-2"></i> Stats
                        </h5>

                        @if(!$user?->privacy)
                            <div class="col">
                                <h6 class="mb-2">Offers Completed</h6>
                                <span>{{ $user?->leads->count() }}</span>
                            </div>
                            <div class="col">
                                <h6 class="mb-2">ERC Earned</h6>
                                <p>{{ number_format($user?->leads->sum('points') ?? 0) }} ERC</p>
                            </div>
                            <div class="col">
                                <h6 class="mb-2">Users Referred</h6>
                                <p>{{ $user?->referrals->count() }}</p>
                            </div>
                        @else
                            <div class="col text-center text-secondary">
                                <svg width="24" height="24" viewBox="0 0 28 22">
                                    <path fill="currentColor"
                                          d="M14 1.7c-.4.02-1.4-.07-2.18-.6-.98-.68-2.64-.98-3.62.22-.95 1.16-1.83 4.74-2.15 6.6C2.39 8.43 0 9.31 0 10.31c0 1.62 6.27 2.93 14 2.93s14-1.31 14-2.93c0-1-2.4-1.88-6.05-2.4-.32-1.86-1.2-5.44-2.15-6.6-.98-1.2-2.64-.9-3.62-.23-.78.54-1.78.63-2.18.6Z"></path>
                                    <path fill="currentColor" fill-rule="evenodd"
                                          d="M5.34 13.85c1.23.2 4.69.6 8.66.6 3.97 0 7.43-.4 8.66-.6v3.37c0 1.32-.96 2.6-1.43 3.08-.4.4-1.55 1.2-2.94 1.2-1.73 0-2.63-.6-3.09-1.05A1.87 1.87 0 0 0 14 20c-.25 0-.84.09-1.2.45-.46.45-1.36 1.05-3.09 1.05-1.39 0-2.53-.8-2.94-1.2a4.94 4.94 0 0 1-1.43-3.08v-3.37ZM7.57 17c.2-.69 1.08-1.2 2.14-1.2s1.94.51 2.14 1.2c-.2.68-1.08 1.2-2.14 1.2s-1.94-.52-2.14-1.2Zm8.54 0c.2-.69 1.08-1.2 2.14-1.2 1.05 0 1.93.51 2.14 1.2-.2.68-1.09 1.2-2.14 1.2-1.06 0-1.94-.52-2.14-1.2Z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                <h5 class="text-secondary mt-1 f-md">Private Profile</h5>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <div class="row">
                        <h5 class="fw-bold text-start text-white">
                            <i class="fa-solid fa-rocket text-secondary me-2"></i> Activity
                        </h5>

                        @if(!$user?->privacy)
                            <div class="table-responsive">
                                <table class="table table-borderless small">
                                    <thead>
                                    <tr>
                                        <th scope="col" style="text-transform: unset !important;">Name</th>
                                        <th scope="col" style="text-transform: unset !important;">Time</th>
                                        <th scope="col" style="text-transform: unset !important;">Reward</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @forelse($leads as $lead)
                                        <tr>
                                            <td class="d-flex align-items-center gap-2">
                                                @if($lead->image)
                                                    <img src="{{ $lead->image }}" alt="{{ $lead->name }}"
                                                         class="rounded-3" style="width: 30px;"
                                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/placeholder-offer.svg') }}';">
                                                @else
                                                    <div
                                                        class="bg-label-primary bg-opacity-50 rounded-3  d-flex align-items-center"
                                                        style="width: 30px; height: 30px;">
                                                        <i class="fa-solid fa-rocket text-primary m-auto mt-2"
                                                           style="font-size: 18px;"></i>
                                                    </div>
                                                @endif
                                                <span class="lead-name">{{ $lead->name }}</span>
                                            </td>
                                            <td class="text-truncate">{{ $lead->created_at->diffForHumans() }}</td>
                                            <td class="text-truncate">
                                                <img src="{{ asset('assets/img/coin.png') }}" style="width: 12px;"
                                                     alt="ERC">
                                                <span class="fw-bold">{{ number_format($lead->points) }} ERC</span>
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
                            <div class="d-flex justify-content-center">
                                @if($leads instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    {{ $leads->links() }}
                                @endif
                            </div>
                        @else
                            <div class="col text-center text-secondary">
                                <svg width="24" height="24" viewBox="0 0 28 22">
                                    <path fill="currentColor"
                                          d="M14 1.7c-.4.02-1.4-.07-2.18-.6-.98-.68-2.64-.98-3.62.22-.95 1.16-1.83 4.74-2.15 6.6C2.39 8.43 0 9.31 0 10.31c0 1.62 6.27 2.93 14 2.93s14-1.31 14-2.93c0-1-2.4-1.88-6.05-2.4-.32-1.86-1.2-5.44-2.15-6.6-.98-1.2-2.64-.9-3.62-.23-.78.54-1.78.63-2.18.6Z"></path>
                                    <path fill="currentColor" fill-rule="evenodd"
                                          d="M5.34 13.85c1.23.2 4.69.6 8.66.6 3.97 0 7.43-.4 8.66-.6v3.37c0 1.32-.96 2.6-1.43 3.08-.4.4-1.55 1.2-2.94 1.2-1.73 0-2.63-.6-3.09-1.05A1.87 1.87 0 0 0 14 20c-.25 0-.84.09-1.2.45-.46.45-1.36 1.05-3.09 1.05-1.39 0-2.53-.8-2.94-1.2a4.94 4.94 0 0 1-1.43-3.08v-3.37ZM7.57 17c.2-.69 1.08-1.2 2.14-1.2s1.94.51 2.14 1.2c-.2.68-1.08 1.2-2.14 1.2s-1.94-.52-2.14-1.2Zm8.54 0c.2-.69 1.08-1.2 2.14-1.2 1.05 0 1.93.51 2.14 1.2-.2.68-1.09 1.2-2.14 1.2-1.06 0-1.94-.52-2.14-1.2Z"
                                          clip-rule="evenodd"></path>
                                </svg>
                                <h5 class="text-secondary mt-1 f-md">Private Profile</h5>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
