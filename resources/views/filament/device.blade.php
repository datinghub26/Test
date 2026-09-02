<div class="flex items-center justify-start" x-data="">
    {{-- OS Icon and Tooltip --}}
    <img
        src="{{ device_image($dd->getOs()['name'] ?? 'unknown') }}"
        alt="{{ $dd->getOs()['name'] ?? 'Unknown OS' }}"
        class="h-8 w-8"
        x-tooltip="{
            content: @js($dd->getOs()['name'] ?? 'Unknown OS'),
            theme: $store.theme,
        }"
    >

    {{-- Browser Icon and Tooltip --}}
    <img
        src="{{ browser_image($dd->getClient()['family'] ?? 'unknown') }}"
        alt="{{ $dd->getClient()['family'] ?? 'Unknown Browser' }}"
        class="ms-1 h-8 w-8"
        x-tooltip="{
            content: @js($dd->getClient()['family'] ?? 'Unknown Browser'),
            theme: $store.theme,
        }"
    >

    {{-- Device Brand and Model --}}
    @if(!empty($showBrand) && ($dd->getBrandName() || $dd->getModel()))
        <span class="px-2 py-1 text-xs">
            {{ $dd->getBrandName() ?? '' }} {{ $dd->getModel() ?? '' }}
        </span>
    @endif
</div>
{{-- Raw User Agent State--}}
@if(!empty($state))
    <span class="py-1 text-xs break-all max-w-full mt-1 block overflow-hidden text-ellipsis">{{ $state }}</span>
@endif
