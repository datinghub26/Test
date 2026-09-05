@props([
    'width' => '200px',
    'height' => 'auto',
])

<a href="{{ url('/') }}">
    <span class="text-center d-inline-flex align-items-center justify-content-center">
        <img src="{{ asset('assets/img/logo.png') }}?v={{ filemtime(public_path('assets/img/logo.png')) }}" alt="Reward Cash" width="{{ $width }}" style="max-height: 48px; object-fit: contain;" class="app-brand-img">
        <img src="{{ asset('assets/img/icon-light.png') }}?v={{ filemtime(public_path('assets/img/icon-light.png')) }}" alt="ERC" width="42px" style="object-fit: contain;" class="app-brand-img-collapsed">
    </span>
</a>
