@props(['orientation' => 'left'])

@php

    $orientationClass = match($orientation) {
        'left' => 'top-7 left-0',
        'right' => 'right-0',
        'down' => 'bottom-0 left-0 -mr-1',
        'down right' => 'bottom-0 right-0 -mr-1',
        default => 'top-7 left-0',
    }

@endphp

<div>
    <div x-data="{ open: false }"
        {{ $attributes->merge(['class' => 'relative']) }}
    >
        <div x-on:click="open = true">
            {{ $trigger }}
        </div>

        <div x-show="open"
             class="absolute z-50 mt-2 w-64 rounded-md shadow-lg origin-top-right {{ $orientationClass }}"
             @click.away="open = false"
             x-cloak
        >
            <div class="rounded-md shadow-xs py-1 bg-white">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
