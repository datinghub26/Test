<div class="flex fi-ta-text items-center justify-between"
     x-data="{}"
     x-tooltip="{
            content: @js($dd->getOs()['name']),
            theme: $store.theme,
        }">
    <img src="{{ browser_image($dd->getClient()['name']) }}" alt="{{ $dd->getOs()['name'] }}" class="h-8 w-8">
</div>
