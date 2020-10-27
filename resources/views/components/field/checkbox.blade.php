@props(['model' => null])

<input
    id="{{ $model }}"
    wire:model="{{ $model }}"
    autocomplete="{{ $model }}"
    {{ $attributes->merge([
        'class' => 'form-checkbox h-5 w-5 text-green-600',
        'type' => 'checkbox',
    ]) }}
/>
