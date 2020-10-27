@props(['active' => false])

@if ($active)
    <x-heroicon-o-check-circle {{ $attributes->merge(['class' => 'text-green-500 h-6 w-6']) }} />
@else
    <x-heroicon-o-check-circle {{ $attributes->merge(['class' => 'text-red-500 h-6 w-6']) }} />
@endif
