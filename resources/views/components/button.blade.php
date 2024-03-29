@props(['inline' => true, 'color' => 'primary'])

@php
    $classes = [];
    $classes[] = $inline ? 'inline-flex' : 'block';

    $classes[] = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 active:bg-primary-900 focus:border-primary-900 text-white',
        'blue' => 'bg-blue-600 hover:bg-blue-700 active:bg-blue-900 focus:border-blue-900 text-white',
        'red' => 'bg-red-600 hover:bg-red-700 active:bg-red-900 focus:border-red-900 text-white',
        'green' => 'bg-green-600 hover:bg-green-700 active:bg-green-900 focus:border-green-900 text-white',
        'yellow' => 'bg-yellow-600 hover:bg-yellow-700 active:bg-yellow-900 focus:border-yellow-900 text-white',
        'rose' => 'bg-rose-600 hover:bg-rose-700 active:bg-rose-900 focus:border-rose-900 text-white',
        'pink' => 'bg-pink-600 hover:bg-pink-700 active:bg-pink-900 focus:border-pink-900 text-white',
        'fuchsia' => 'bg-fuchsia-600 hover:bg-fuchsia-700 active:bg-fuchsia-900 focus:border-fuchsia-900 text-white',
        'purple' => 'bg-purple-600 hover:bg-purple-700 active:bg-purple-900 focus:border-purple-900 text-white',
        'violet' => 'bg-violet-600 hover:bg-violet-700 active:bg-violet-900 focus:border-violet-900 text-white',
        'indigo' => 'bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-900 focus:border-indigo-900 text-white',
        'sky' => 'bg-sky-600 hover:bg-sky-700 active:bg-sky-900 focus:border-sky-900 text-white',
        'cyan' => 'bg-cyan-600 hover:bg-cyan-700 active:bg-cyan-900 focus:border-cyan-900 text-white',
        'teal' => 'bg-teal-600 hover:bg-teal-700 active:bg-teal-900 focus:border-teal-900 text-white',
        'emerald' => 'bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-900 focus:border-emerald-900 text-white',
        'lime' => 'bg-lime-600 hover:bg-lime-700 active:bg-lime-900 focus:border-lime-900 text-white',
        'amber' => 'bg-amber-600 hover:bg-amber-700 active:bg-amber-900 focus:border-amber-900 text-white',
        'orange' => 'bg-orange-600 hover:bg-orange-700 active:bg-orange-900 focus:border-orange-900 text-white',
        'warmGray' => 'bg-warmGray-600 hover:bg-warmGray-700 active:bg-warmGray-900 focus:border-warmGray-900 text-white',
        'trueGray' => 'bg-trueGray-600 hover:bg-trueGray-700 active:bg-trueGray-900 focus:border-trueGray-900 text-white',
        'gray' => 'bg-gray-600 hover:bg-gray-700 active:bg-gray-900 focus:border-gray-900 text-white',
        'coolGray' => 'bg-coolGray-600 hover:bg-coolGray-700 active:bg-coolGray-900 focus:border-coolGray-900 text-white',
        'blueGray' => 'bg-blueGray-600 hover:bg-blueGray-700 active:bg-blueGray-900 focus:border-blueGray-900 text-white',
        'white' => 'bg-white hover:bg-white active:bg-black focus:border-black text-black',
    ][$color];
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => implode(' ', $classes) . ' items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-widest focus:outline-none focus:shadow-outline-primary disabled:opacity-25 transition ease-in-out duration-150'
]) }}>
    {{ $slot }}
</button>
