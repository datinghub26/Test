@props([
    'width' => '35px',
    'height' => '35px',
    'fill' => '#37E780',
])

<a href="{{ url('/') }}">
{{--    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="200px"/>--}}
    <span class="text-center">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" width="200px" class="app-brand-img">
        <img src="{{ asset('assets/img/icon-light.png') }}" alt="Logo" width="45px" class="app-brand-img-collapsed">
    </span>
</a>
