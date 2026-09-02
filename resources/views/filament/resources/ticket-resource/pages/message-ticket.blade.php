<x-filament-panels::page>
    <div class="space-y-6">
        <div
            class="border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-6 py-4 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Ticket # {{ $ticket->id }}</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket->title }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at->diffForHumans() }}</p>
                </div>

                @if($ticket->isOpen())
                    <span
                        class="px-3 py-1 text-sm font-medium text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 rounded-full">
                        Active
                    </span>
                @else
                    <span
                        class="px-3 py-1 text-sm font-medium text-red-700 dark:text-red-400 bg-red-100 dark:bg-red-900/30 rounded-full">
                        Closed
                    </span>
                @endif
            </div>
        </div>

        <div class="px-6 py-4 bg-white dark:bg-gray-900 rounded-lg shadow">
            <p class="text-gray-700 dark:text-gray-300">Attached Files</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-4 w-full">
                @foreach($ticket->media as $media)
                    <div class="flex items-center gap-1 p-2 bg-gray-100 dark:bg-gray-800/50 rounded-lg shadow">
                        <x-heroicon-o-paper-clip class="w-4 h-4 text-gray-500 dark:text-gray-400"/>
                        <a href="{{ $media->getUrl() }}" target="_blank" class="text-sm text-blue-500 hover:underline">
                            {{ $media->file_name }}
                        </a>
                        <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $media->human_readable_size }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Messages --}}
        <div class="px-6 py-4 max-h-[500px] overflow-y-auto space-y-6 bg-white dark:bg-gray-900 rounded-lg shadow">
            @foreach($ticket->messages as $reply)
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-shrink-0">
                        <img
                            src="{{ $reply->user->avatar() }}"
                            alt="{{ $reply->user->username }}"
                            class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover ring-2 ring-gray-200 dark:ring-gray-700"
                        />
                    </div>
                    <div class="flex-grow">
                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-2 space-x-2">
                                <div>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $reply->user->username }}
                                    </span>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $reply->user->email }}
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                     <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $reply->created_at->diffForHumans() }}
                                    </span>

                                    {{ ($this->deleteAction)(['message' => $reply->id]) }}
                                </div>

                            </div>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed break-words">
                                {!! nl2br($reply->message) !!}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</x-filament-panels::page>

<x-filament-actions::modals/>
