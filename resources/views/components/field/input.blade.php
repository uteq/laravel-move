@props(['disabled' => false, 'model'])

<input {{ $disabled ? 'disabled' : '' }}
       id="{{ $model }}"
       wire:model.defer="{{ $model }}"
       autocomplete="{{ $model }}"
    {!! $attributes->merge(['class' => 'flex-1 form-input block w-full min-w-0 rounded-none rounded-md transition duration-150 ease-in-out sm:text-sm sm:leading-5']) !!}
/>
