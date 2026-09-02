<div class="flex fi-ta-text items-center justify-between">
    <x-filament::icon
        alias="flags::{{ strtolower($state) }}"
        class="h-5 w-6"
    />

    <span  style="margin-left: 4px">
        {{ $state }}
    </span>
</div>
