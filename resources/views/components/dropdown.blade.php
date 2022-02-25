@props(['orientation' => 'left', 'withMargin' => true, 'top' => 'top-7'])

<div x-data="{ open: false }"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <div x-on:click="open = true" {{ $trigger->attributes }}>
        {{ $trigger }}
    </div>

    <div :class="{'block': open, 'hidden': ! open}"
         class="absolute z-50 {{ $withMargin ? 'mt-2' : null }} {{ $top }} w-48 rounded-md shadow-lg origin-top-right {{ $orientation === 'left' ? 'left-0' : 'right-0' }} hidden"
         @click.away="open = false"
    >
        <div class="rounded-md shadow py-1 bg-white">
            {{ $slot }}
        </div>
    </div>
</div>
