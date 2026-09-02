<div {{--wire:poll.60s="checkForNewNotifications"--}}
    wire:init="loadNotifications"
>
    <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
        <a class="nav-link dropdown-toggle hide-arrow mt-1" href="javascript:void(0);"
           wire:click="markAllAsRead"
           data-bs-toggle="dropdown"
           data-bs-auto-close="outside"
           aria-expanded="false">

            <i class="fa-solid fa-bell" style="font-size: 22px"></i>
            @if($unreadNotificationsCount > 0)
                <span class="badge bg-label-primary rounded-pill badge-notifications">
                    {{ $unreadNotificationsCount }}
                </span>
            @endif
        </a>
        <ul class="dropdown-menu dropdown-menu-end py-0"
            @click.away="if (!$event.target.closest('.dropdown-notifications') && $el.classList.contains('show')) $el.classList.remove('show');"
            wire:ignore.self>
            <li class="dropdown-menu-header border-bottom">
                <div class="dropdown-header d-flex align-items-center py-3">
                    <h5 class="text-body mb-0 me-auto f-md">Notification</h5>
                    <a wire:click="markAllAsRead"
                       href="javascript:void(0)"
                       class="dropdown-notifications-all text-body" data-bs-toggle="tooltip"
                       data-bs-placement="top" title="Mark all as read">
                        <x-heroicon-o-envelope-open width="20" height="20"/>
                    </a>
                </div>
            </li>
            <li class="dropdown-notifications-list scrollable-container" wire:ignore.self>
                <ul class="list-group list-group-flush">
                    @foreach($notifications as $notification)
                        <li
                            @if($notification->href)
                                onclick="window.location.href='{{ $notification->href }}'"
                            @endif
                            class="list-group-item list-group-item-action dropdown-notifications-item">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        @if($notification->type == 'success')
                                            <span class="avatar-initial rounded-circle bg-label-success">
                                                <x-heroicon-o-check-circle width="24" height="24"/>
                                            </span>
                                        @elseif($notification->type == 'danger')
                                            <span class="avatar-initial rounded-circle bg-label-danger">
                                            <x-heroicon-o-exclamation-circle width="24" height="24"/>
                                        @elseif($notification->type == 'warning')
                                                    <span class="avatar-initial rounded-circle bg-label-warning">
                                                <x-heroicon-o-exclamation-triangle width="24" height="24"/>
                                            </span>
                                                @else
                                                    <span class="avatar-initial rounded-circle bg-label-info">
                                                <x-heroicon-o-information-circle width="24" height="24"/>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1 f-md">{{ $notification->title }}</p>
                                    <p class="mb-0 small">{{ $notification->message }}</p>
                                    <span style="font-size: 12px" class="text-secondary">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <div class="flex-shrink-0 dropdown-notifications-actions"></div>
                            </div>
                        </li>
                    @endforeach

                </ul>
            </li>
            <li class="dropdown-menu-footer border-top">
                <a href="javascript:void(0);"
                   wire:click="markAllAsRead"
                   class="btn btn-label-secondary d-flex p-2 h-px-40 mb-1 align-items-center m-2 f-md fw-normal"
                   style="">
                    Mark all as read
                </a>
            </li>
        </ul>
    </li>
</div>
