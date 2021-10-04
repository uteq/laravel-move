@props([
    'withPadding' => true,
    'withoutPadding' => false,
    'shadow' => 'none',
    'headerWithoutPadding' => false,
    'heading' => null,
    'headingWithoutStyle' => false,
    'foot' => null,
    'footWithoutPadding' => false,
    'withLoader' => false,
    'loaderTarget' => null,
    'collapsable' => false,
    'closed' => false,
])

@php
    $defaultPadding = 'py-5 px-4';
    $padding = $withoutPadding ? '' : ($withPadding ? $defaultPadding : '');
    $open = ! $closed;
@endphp

@php
    $conditionalAttributes = $withLoader ? [
        'wire:loading.class' => 'relative overflow-hidden',
        'wire:target' => $loaderTarget,
    ] : [];
@endphp

<div
    {{ $attributes->merge(array_replace([
        'class' => 'relative '. ($closed ?: 'border-b') .' border-t border-gray-200 bg-white sm:border overflow-hidden sm:rounded-lg mb-6 ' . ($heading ? '' : $padding) .' shadow-' . $shadow
    ], $conditionalAttributes)) }}
    x-data="{ open : '{{ $open }}' }"
>
    @if ($withLoader && $open)
        <x-move-loader target="{{ $loaderTarget }}" />
    @endif

    @if ($heading)
        <div class="bg-white border-b border-gray-200 {{ $headerWithoutPadding ? null : 'px-4 py-2' }} {{ $collapsable ? 'cursor-pointer' : null }}"
             <?php if ($collapsable): ?> x-on:click="open = ! open" <?php endif; ?>
        >
            @if (! $headingWithoutStyle)
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
            @endif
                        {!! $heading !!}
            @if (! $headingWithoutStyle)
                    </h3>
                </div>
            </div>
            @endif
        </div>
        <div x-cloak x-show.transitions.duration.1000ms="open" class="divide-y divide-gray-200 {{ $withoutPadding ? null : $defaultPadding }}">
            {{ $slot }}
        </div>
    @else
        <div x-cloak x-show.transitions.duration.1000ms="open">
            {{ $slot }}
        </div>
    @endif

    @if ($foot)
        <div x-cloak x-show.transitions.duration.1000ms="open" class="bg-white border-t border-gray-200 {{ $footWithoutPadding ? null : 'px-4 py-2' }}">
            {!! $foot !!}
        </div>
    @endif
</div>
