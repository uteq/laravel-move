@props(['disabled' => false, 'model'])

<input {{ $disabled ? 'disabled' : '' }}
       id="{{ $model }}"
       wire:model.lazy="{{ $model }}"
       autocomplete="{{ $model }}"
       type="password"
    {!! $attributes->merge(['class' => 'flex-1 form-input block w-full min-w-0 rounded-md transition duration-150 ease-in-out sm:text-sm sm:leading-5 border-gray-300']) !!}
/>
