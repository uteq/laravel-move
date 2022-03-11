@props(['inline' => true, 'color' => 'primary'])

@php
$classes = [];
$classes[] = $inline ? 'inline-flex' : 'block';

$classes[] = [
    'primary' => 'bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500',
    'blue' => 'bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
    'red' => 'bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500',
    'green' => 'bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500',
    'yellow' => 'bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500',
    'rose' => 'bg-rose-600 hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500',
    'pink' => 'bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500',
    'fuchsia' => 'bg-fuchsia-600 hover:bg-fuchsia-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fuchsia-500',
    'purple' => 'bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500',
    'violet' => 'bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500',
    'indigo' => 'bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
    'sky' => 'bg-sky-600 hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500',
    'cyan' => 'bg-cyan-600 hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500',
    'teal' => 'bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500',
    'emerald' => 'bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500',
    'lime' => 'bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500',
    'amber' => 'bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500',
    'orange' => 'bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500',
    'warmGray' => 'bg-warmGray-600 hover:bg-warmGray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-warmGray-500',
    'trueGray' => 'bg-trueGray-600 hover:bg-trueGray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-trueGray-500',
    'gray' => 'bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500',
    'coolGray' => 'bg-coolGray-600 hover:bg-coolGray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-coolGray-500',
    'blueGray' => 'bg-blueGray-600 hover:bg-blueGray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blueGray-500',
    'white' => 'bg-white hover:bg-white active:bg-black focus:border-black text-black',
][$color];
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => implode(' ', $classes) . ' items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:shadow-outline-primary disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
