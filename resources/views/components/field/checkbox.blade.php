@props(['model' => null, 'value' => null])

<input
    id="{{ $model }}"
    wire:model="{{ $model }}"
    autocomplete="{{ $model }}"
    value="{{ $value }}"
    {{ $attributes->merge([
        'class' => 'form-tick appearance-none h-5 w-5 border border-gray-300 rounded-md checked:bg-green-600 checked:border-transparent focus:outline-none text-green-600',
        'type' => 'checkbox',
    ]) }}
/>
