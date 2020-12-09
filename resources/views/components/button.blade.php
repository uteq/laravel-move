@props(['inline' => true])

@php
$classes = [];
$classes[] = $inline ? 'inline-flex' : 'block';
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => implode(' ', $classes) . ' items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:shadow-outline-green disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
