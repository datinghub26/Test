@props([
    'coins' => 0,
    'showCoinOnly' => false,
    'showUSDOnly' => false,
    'size' => '13px'
])

<div class="d-flex align-items-center">
    <img x-show="is_coin == '1'" src="{{ asset('assets/img/coin.png') }}" alt="" width="{{ $size }}" class="me-1">
    <span x-text="is_coin == '1' ? '{{ number_format($coins) }}' : '${{ to_money_str($coins) }}'"
          style="font-size: {{ $size }}"></span>
</div>
