<div class="flex items-center gap-2">
    @if($image)
        <img src="{{ $image }}" class="rounded-md" style="width: 30px;" alt="">
    @else
        <div class="rounded-md flex items-center justify-center" style="width: 30px; height: 30px; background: #1bab68">
            <x-heroicon-c-rocket-launch class="h-5 w-6"/>
        </div>
    @endif

    {{ $name }}
</div>
