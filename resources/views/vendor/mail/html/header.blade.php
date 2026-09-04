@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Coinhub' || trim($slot) === 'ERC' || trim($slot) === config('app.name'))
<img src="{{ url(asset('assets/img/logo.png')) }}" class="logo" alt="{{ config('app.name') }}">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
