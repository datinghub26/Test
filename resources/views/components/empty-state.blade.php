@props([
    'message' => 'No data available.',
])

<div wire:loading.remove {{ $attributes->merge(['class' => 'row text-center text-secondary']) }}>
    <x-heroicon-s-archive-box-x-mark style="width: 75px; height: 75px; margin: auto"/>
    <p class="text-secondary">{{ $message }}</p>
</div>
