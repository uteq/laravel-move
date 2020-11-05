@props(['model' => null, 'value' => null])

<input
    id="{{ $model }}"
    wire:model="{{ $model }}"
    autocomplete="{{ $model }}"
    value="{{ $value }}"
    {{ $attributes->merge([
        'class' => 'form-checkbox h-5 w-5 text-green-600',
        'type' => 'checkbox',
    ]) }}
/>
