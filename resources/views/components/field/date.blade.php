@props([
    'disabled' => false,
    'model' => $attributes->wire('model')->value(),
    'key' => $attributes->wire('key')->value() ?? null,
    'config' => []
])

@php $key = $key ?: $model @endphp

<div class="relative w-full flex flatpickr" wire:ignore wire:key="{{ $key }}">
    <input
        {{ $disabled ? 'disabled' : '' }}
        id="{{ $model }}"
        wire:model.lazy="{{ $model }}"
        wire:dirty.class="loading"
        autocomplete="{{ $model }}"
        placeholder="{{ $placeholder ?? 'Kies een datum' }}"
        x-data="{
            destroy() { console.log('destroy', $el) },
        }"
        x-ref="input"
        x-init="
            window.flatpickr($refs.input, {{ json_encode($config) }})
        "
        {!! $attributes->merge(['class' => 'flex-1 form-input block w-full min-w-0 rounded-md transition duration-150 ease-in-out sm:text-sm sm:leading-5 border-gray-300 rounded']) !!}
    />

    <div class="absolute top-0 right-0 px-3 py-2" data-toggle>
        <svg class="h-6 w-6 text-gray-400"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </div>
</div>
