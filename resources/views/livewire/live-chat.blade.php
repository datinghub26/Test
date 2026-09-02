@assets
<style>
    .custom.right {
        text-align: right;
    }

    .custom.left {
        text-align: left;
    }

    .custom-chat-container {
        width: 100%;
        max-width: 400px;
        height: 80vh;
        position: fixed;
        bottom: 0;
        right: 0;
        z-index: 9999;
        opacity: 0;
        transform: translateY(100%);
        transition: opacity .3s, transform .3s, box-shadow .3s;
        box-shadow: 0 0 10px rgba(0, 0, 0, .2);
        /*font-family: 'Inter', sans-serif;*/
    }

    @media (max-width: 768px) {
        .custom-chat-container {
            height: 100%;
            max-width: 100%;
        }
    }

    .custom-chat-container p, .custom-chat-container span {
        font-size: 13px !important;
        /*line-height: 1;*/
    }

    .custom-chat-container strong {
        font-size: 14px !important;
    }

    .custom-chat-container.active {
        opacity: 1;
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(0, 0, 0, .4)
    }

    .custom-chat-box {
        padding: 1rem;
        height: 100%;
        background-color: var(--bs-base);
        border-radius: 10px;
        overflow-y: auto;
        display: flex;
        flex-direction: column
    }

    .custom-chat-icon {
        display: flex;
        position: fixed;
        bottom: 20px;
        right: 20px;
        background-color: var(--bs-primary);
        color: #fff;
        padding: .5rem;
        border-radius: 999px;
        cursor: pointer;
        transition: opacity .3s;
        z-index: 9999;
        gap: 0.5rem;
    }

    @media (max-width: 1200px) {
        .custom-chat-icon {
            bottom: 100px;
        }
    }

    .custom-chat-icon.hidden {
        opacity: 0.00001;
        visibility: hidden
    }

    .custom-chat-icon.visible {
        opacity: 1;
        visibility: visible
    }

    .custom-card-header {
        padding: .5rem 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center
    }

    .custom-card-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        overflow-y: auto;
        flex-grow: 1
    }

    .custom-chat-box .custom-card-body::-webkit-scrollbar {
        width: 3px
    }

    .custom-chat-box .custom-card-body::-webkit-scrollbar-track {
        background: #3a3e4b;
        border-radius: 10px
    }

    .custom-chat-box .custom-card-body::-webkit-scrollbar-thumb {
        background-color: var(--bs-primary);
        border-radius: 10px;
        border: 2px solid transparent
    }

    .custom-chat-box .custom-card-body::-webkit-scrollbar-thumb:hover {
        background-color: #4a49b8
    }

    .custom-btn-primary {
        background-color: var(--bs-base);
        border: none;
        color: #fff;
        padding: .3rem 1rem;
        border-radius: 5px;
        cursor: pointer
    }

    .custom-btn-primary:hover {
        background-color: var(--bs-primary)
    }

    .custom-card-footer {
        padding: 1rem .5rem .5rem;
        background-color: var(--bs-base);
    }

    .custom-input-group {
        display: flex;
        border: 1px solid var(--bs-primary);
        border-radius: 5px;
        overflow: hidden
    }

    .custom-form-control {
        flex-grow: 1;
        border: none;
        padding: .5rem;
        background-color: #1d1e26;
        color: #fff
    }

    .custom-form-control:focus {
        outline: 0
    }

    .custom-input-group-text {
        background-color: var(--bs-primary);
        color: #fff;
        border: none;
        padding: .5rem 2rem;
        cursor: pointer
    }

    .custom-input-group-text:hover {
        background-color: #4a49b8
    }

    .custom-card-body .progress-circle {
        width: 45px !important;
        height: 45px !important;
    }

    .custom-card-body .progress-circle:before {
        width: 40px !important;
        height: 40px !important;
    }
</style>
@endassets

<div x-data="{isOpen: false}" x-on:open-chat.window="isOpen = true"
{{--     wire:init="dispatch('scroll-chat-to-bottom')"--}}
{{--     x-init="$watch('isOpen', value => $dispatch('scroll-chat-to-bottom'))"--}}
>
    <div
        :class="{'custom-chat-icon rounded bg-label-primary d-xl-block d-none': true, 'hidden': isOpen, 'visible': !isOpen}"
        @click="isOpen = !isOpen">
        <x-heroicon-s-chat-bubble-bottom-center-text style="width: 24px; height: 24px"/>
    </div>

    <div class="custom-chat-container" :class="{ 'active': isOpen }">
        <div class="custom-chat-box" x-init="$dispatch('chat-init')">
            <!-- Chat Header -->
            <div class="custom-card-header">
                <p class="badge bg-label-primary d-flex align-items-center mb-0">
                    <x-heroicon-o-users style="width: 22px; height: 22px" class="me-1"/>

                    {{--                    <i class="fas fa-circle me-2" style="color: var(--bs-primary)"></i>--}}
                    <span id="online-users" class="f-md"
                          x-data="{ onlineUsers: {{ $this->totalOnlineUsers() }} }"
                          x-init="() => {
                try {
                @auth
                    Echo.join('online-users')
                        .here(users => {
                            onlineUsers = users.length;
                        })
                        .joining(user => {
                            onlineUsers++;
                        })
                        .leaving(user => {
                            onlineUsers--;
                        });
                @endauth
                } catch (e) {
                    //console.error('Failed to connect to Echo channel:', e);
                }}
              "
                          x-text="onlineUsers">0
                    </span>
                </p>
                <button type="button" class="custom-btn-primary btn-sm" @click="isOpen = false" aria-label="Close chat">
                    <x-heroicon-s-arrow-left style="width: 24px; height: 24px"/>
                </button>
            </div>

            <!-- Chat Body (Scrollable area) -->
            <div class="custom-card-body" style="overflow-y: auto;" wire:ignore.self>
                @forelse($messages as $message)
                    <div class="d-flex align-items-start gap-2 mb-2 bg-label-secondary py-2 px-3 rounded message">
                        <div style="cursor: pointer"
                             @click="$dispatch('activity-open', {user_id: '{{ $message->user_id }}'})">

                            @php
                                $progress = $message->user?->levelProgress() ?? 0;
                            @endphp

                            <div class="progress-circle"
                                 style="background: conic-gradient(var(--bs-primary) 0% {{ $progress }}%, var(--bs-dark) {{ $progress }}% 100%);">
                                <img src="{{ $message->user?->avatar() }}" alt="{{ $message->user?->username }}"
                                     class="rounded-circle"
                                     style="width: 35px !important; height: 35px !important;">

                                <div class="badge bg-primary rounded-circle text-white"
                                     style="position: absolute; bottom: 0; right: 0; font-size: 10px; padding: 2px 5px; z-index: 11">
                                    {{ $message->user?->level ?? 1 }}
                                </div>
                            </div>

                            {{--                            <img src="{{ $message->user->avatar() }}" alt="avatar" class="rounded-circle"--}}
                            {{--                                 style="width: 35px; height: 35px;">--}}
                        </div>
                        <div style="line-height: 1.2; width: 100%"
                             x-data="{ showMention: false }"
                             @mouseover="showMention = true" @mouseleave="showMention = false">
                            <span class="d-flex align-items-center justify-content-between mb-1 text-white">
                                <div style="cursor: pointer"
                                     @click="$dispatch('activity-open', {user_id: '{{ $message->user_id }}'})">
                                        {{ $message->user->username }}
                                    @if($message->user->role == 'admin')
                                        <i class="fas fa-shield" style="color: #da2d2d; font-size: 11px"></i>
                                        <span class="text-secondary" style="font-size: 10px !important;">Admin</span>
                                    @endif
                                </div>

                                <span class="text-secondary small text-end d-flex align-items-center"
                                      style="font-size: 10px !important;">
                                    {{ $message->created_at->format('h:i A') }}

                                     <span class="mx-2 text-primary" style="font-size: 11px !important; cursor: pointer"
                                           x-show.important="showMention" x-transition
                                           @click="document.getElementById('message-to-send').value += '{{ "@{$message->user->username}"}}'; document.getElementById('message-to-send').focus();">
                                        <i class="fas fa-at"></i>
                                    </span>
                                </span>

                            </span>
                            <span class="text-body">
                                @php
                                    $mentionedUser = $message->getMentionedUser();
                                @endphp

                                @if($mentionedUser)
                                    @php
                                        $username = $message->extractUsernameFromMessage() ?? '';
                                        $messageBody = str_replace('@' . $username, '', $message->message);
                                    @endphp
                                    <span class="text-decoration-underline text-primary" style="cursor: pointer"
                                          @click="$dispatch('activity-open', {user_id: '{{ $mentionedUser->id }}'})">{{ "@$username" }}</span>

                                    {{ $messageBody }}
                                @else
                                    {{ $message->message }}
                                @endif
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="text-center">
                        <p class="mb-0">No messages yet</p>
                    </div>
                @endforelse
            </div>

            <!-- Chat Input -->
            <div class="custom-card-footer pt-3 pb-1 px-2">

                <div class="d-flex position-absolute left-0" style="bottom: 100px; z-index: 99999999; left: 0"
                     wire:ignore
                     id="emoji-picker">
                </div>

                @if($isAbleToChat)
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Type a message..."
                               wire:keydown.enter="sendMessage" wire:model="message" id="message-to-send">
                        <button class="btn btn-outline-secondary" type="button" id="emoji-picker-btn" wire:ignore.self>
                            <i class="fas fa-smile"></i>
                        </button>

                        <button class="btn btn-outline-secondary" type="button" wire:click="sendMessage"
                                id="send-message-btn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>


                    {{--                    <div class="custom-input-group text-box p-0">--}}
                    {{--                        <input class="custom-form-control input-txt-bx" id="message-to-send" type="text"--}}
                    {{--                               name="message-to-send" wire:keydown.enter="sendMessage" wire:model="message"--}}
                    {{--                               placeholder="Type a message...">--}}
                    {{--                        <button class="custom-input-group-text btn-sm input-group-text" type="button" id="sendMessage"--}}
                    {{--                                wire:click="sendMessage">--}}
                    {{--                            <i class="fas fa-paper-plane"></i>--}}
                    {{--                        </button>--}}
                    {{--                    </div>--}}
                @else
                    <button class="btn btn-primary w-100" type="button"
                            data-bs-target="#authModal" data-bs-toggle="modal">
                        Login to chat
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
