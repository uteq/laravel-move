@props(['orientation' => 'left'])

<div>
    <div x-data="{ open: false }"
         {{ $attributes->merge(['class' => 'relative']) }}
    >
        <div x-on:click="open = true">
            {{ $trigger }}
        </div>

        <div :class="{'block': open, 'hidden': ! open}"
             class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right top-7 {{ $orientation === 'left' ? 'left-0' : 'right-0' }} hidden"
             @click.away="open = false"
        >
            <div class="rounded-md shadow-xs py-1 bg-white">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
