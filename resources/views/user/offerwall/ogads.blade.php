@extends('layouts.offerwall')

@section('title', 'Offerwalls')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold mb-2">Offerwalls</h1>
            <p class="text-gray-600">Complete offers to earn ERC for rewards</p>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form action="{{-- route('offerwalls.index') --}}" method="GET"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="category"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Device Filter -->
                <div>
                    <label for="device" class="block text-sm font-medium text-gray-700 mb-1">Device</label>
                    <select name="device" id="device"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Devices</option>
                        @foreach($devices as $device)
                            <option value="{{ $device }}" {{ request('device') == $device ? 'selected' : '' }}>
                                {{ ucfirst($device) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Filter -->
                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort" id="sort"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="" {{ !request('sort') ? 'selected' : '' }}>Default</option>
                        <option value="points_high" {{ request('sort') == 'points_high' ? 'selected' : '' }}>ERC
                            (High to Low)
                        </option>
                        <option value="points_low" {{ request('sort') == 'points_low' ? 'selected' : '' }}>ERC (Low
                            to High)
                        </option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="flex items-end">
                    <button type="submit"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        @if($offers->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="flex flex-col items-center">
                    <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">No offers available with the selected filters.</p>
                    <p class="text-gray-500 mt-1">Try changing your filters or check back later.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($offers as $offer)
                    <div
                        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 offer-card flex flex-col justify-between">
                        <div class="flex p-4">
                            <div class="relative w-[100px] h-[100px] flex-shrink-0 overflow-hidden rounded-md">
                                <img src="{{ $offer['image'] }}" alt="{{ $offer['title'] }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.onerror=null; this.src='{{ asset('assets/img/placeholder-offer.svg') }}';">
                            </div>
                            <div class="ml-4 flex flex-col justify-between flex-grow">
                                <div>
                                    <h2 class="font-bold pr-2"
                                        title="{{ $offer['title'] }}">{{ $offer['title'] }}</h2>
                                    <p class="text-gray-600 text-sm mb-4 h-100 overflow-hidden">
                                        {{ \Illuminate\Support\Str::limit($offer['description'], 50) }}
                                    </p>
                                </div>


                                @if(!empty($offer['devices']))
                                    <div class="flex space-x-1">
                                        @foreach($offer['devices'] as $device)
                                            <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                                @if($device === 'Desktop')
                                                    <i class="fas fa-desktop"></i>
                                                @elseif($device === 'Android')
                                                    <i class="fab fa-android"></i>
                                                @elseif($device === 'iPhone' || $device === 'iOS' || $device === 'iPad')
                                                    <i class="fab fa-apple"></i>
                                                @else
                                                    <i class="fas fa-circle"></i>
                                                @endif
                                        </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 pt-0">
                            @if(!empty($offer['categories']))
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach($offer['categories'] as $category)
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded">
                                        {{ $category }}
                                    </span>
                                    @endforeach
                                </div>
                            @endif

                            <a href="{{ $offer['url'] }}" target="_blank"
                               class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 rounded-md transition-colors duration-300">
                                Up to  {{ number_format($offer['total_points']) }} ERC

                            </a>
                        </div>
                    </div>
                @endforeach
            </div>


            {{--            <div class="mt-8">--}}
            {{--                {{ $offers->links() }}--}}
            {{--            </div>--}}
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        // Auto-submit form when filters change (optional)
        $(document).ready(function () {
            $('#category, #device, #sort').on('change', function () {
                $(this).closest('form').submit();
            });
        });
    </script>
@endpush
