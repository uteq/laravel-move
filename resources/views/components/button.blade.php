@props(['inline' => true])

@php
$classes = [];
$classes[] = $inline ? 'inline-flex' : 'block';
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => implode(' ', $classes) . ' items-center px-4 py-2 bg-'. move()::getThemeColor() .'-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-'. move()::getThemeColor() .'-700 active:bg-'. move()::getThemeColor() .'-900 focus:outline-none focus:border-'. move()::getThemeColor() .'-900 focus:shadow-outline-'. move()::getThemeColor() .' disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
