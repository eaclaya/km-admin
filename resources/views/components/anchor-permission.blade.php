@props(['code'])
@if(isset($code))
    @if(Auth::user()->realUser()->_can($code))
        <a {{ $attributes }} >
            {{ $slot }}
        </a>
    @endif
@else
    <a {{ $attributes }} >
        {{ $slot }}
    </a>
@endif
