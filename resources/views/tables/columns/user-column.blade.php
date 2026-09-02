@php
    $user = $getState();
@endphp
@if($user)
    <div class="flex items-center gap-2 w-auto">
        <img src="{{ $user['avatar'] }}" class="w-8 h-8 rounded-full max-w-none" alt="{{ $user['username'] }}">

        <div>
            <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2"
                 style="line-height: unset">
                <span class="order-2">{{ $user['username'] }}</span>
                @if($shouldRedirect)
                    <a href="{{ \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $user['id']]) }}"
                       class="font-medium text-gray-400 hover:text-primary-400 order-1">
                        <x-heroicon-s-arrow-top-right-on-square class="w-4 h-4 inline-block"/>
                    </a>
                @endif
            </div>

            <div class="text-sm text-gray-500 truncate">{{ $user['email'] }}</div>
        </div>
    </div>
@endif

{{--<div class="flex items-center gap-2 w-auto">--}}
{{--    <img src="{{ $user->avatar() }}" class="w-8 h-8 rounded-full max-w-none" alt="{{ $user->username }}">--}}

{{--    <div>--}}
{{--        <div class="text-sm font-medium text-gray-900 dark:text-white flex items-center gap-2"--}}
{{--             style="line-height: unset">--}}
{{--            <span class="order-2">{{ $user->username }}</span>--}}

{{--            @if($shouldRedirect)--}}
{{--                <a href="{{ \App\Filament\Resources\UserResource::getUrl('edit', ['record' => $user]) }}"--}}
{{--                   class="font-medium text-gray-400 hover:text-primary-400 order-1">--}}
{{--                    <x-heroicon-s-arrow-top-right-on-square class="w-4 h-4 inline-block"/>--}}
{{--                </a>--}}
{{--            @endif--}}
{{--        </div>--}}

{{--        <div class="text-sm text-gray-500 truncate">{{ $user->email }}</div>--}}
{{--    </div>--}}
{{--</div>--}}
