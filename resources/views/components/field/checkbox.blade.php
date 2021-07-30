@props(['model' => null, 'value' => null, 'hasError' => false])

<input
    id="{{ $model }}"
    wire:model="{{ $model }}"
    autocomplete="{{ $model }}"
    value="{{ $value }}"
    {{ $attributes->merge([
        'class' => 'form-tick appearance-none h-5 w-5 border rounded-md checked:bg-primary-600 checked:border-transparent focus:outline-none text-primary-600 ' . ( $hasError ? 'border-red-500' : 'border-gray-300' ),
        'type' => 'checkbox',
    ]) }}
/>
