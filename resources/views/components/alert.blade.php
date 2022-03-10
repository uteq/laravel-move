@props(['title' => null, 'borderPosition' => 'top', 'color' => 'teal', 'hideIcon' => false, 'customProperties' => []])

@php
    extract($customProperties, EXTR_OVERWRITE);

    $classes = [];
    $classes[] = [
        'primary' => 'bg-primary-100 border-primary-500 text-primary-900',
        'white' => 'bg-white border-white text-white',
        'blue' => 'bg-blue-100 border-blue-500 text-blue-900',
        'red' => 'bg-red-100 border-red-500 text-red-900',
        'green' => 'bg-green-100 border-green-500 text-green-900',
        'yellow' => 'bg-yellow-100 border-yellow-500 text-yellow-900',
        'rose' => 'bg-rose-100 border-rose-500 text-rose-900',
        'pink' => 'bg-pink-100 border-pink-500 text-pink-900',
        'fuchsia' => 'bg-fuchsia-100 border-fuchsia-500 text-fuchsia-900',
        'purple' => 'bg-purple-100 border-purple-500 text-purple-900',
        'violet' => 'bg-violet-100 border-violet-500 text-violet-900',
        'indigo' => 'bg-indigo-100 border-indigo-500 text-indigo-900',
        'sky' => 'bg-sky-100 border-sky-500 text-sky-900',
        'cyan' => 'bg-cyan-100 border-cyan-500 text-cyan-900',
        'teal' => 'bg-teal-100 border-teal-500 text-teal-900',
        'emerald' => 'bg-emerald-100 border-emerald-500 text-emerald-900',
        'lime' => 'bg-lime-100 border-lime-500 text-lime-900',
        'amber' => 'bg-amber-100 border-amber-500 text-amber-900',
        'orange' => 'bg-orange-100 border-orange-500 text-orange-900',
        'warmGray' => 'bg-warmGray-100 border-warmGray-500 text-warmGray-900',
        'trueGray' => 'bg-trueGray-100 border-trueGray-500 text-trueGray-900',
        'gray' => 'bg-gray-100 border-gray-500 text-gray-900',
        'coolGray' => 'bg-coolGray-100 border-coolGray-500 text-coolGray-900',
        'blueGray' => 'bg-blueGray-100 border-blueGray-500 text-blueGray-900',
    ][$color];

    $borderClass = [
        'top' => 'border-t-4 rounded-b',
        'left' => 'border-l-4 rounded-r',
        'right' => 'border-r-4 rounded-l',
        'bottom' => 'border-b-4 rounded-t',
    ][$borderPosition];
@endphp

<div
    {{ $attributes->class(array_merge($classes, [
        $borderClass .' px-4 py-3 shadow-md',
    ])) }}
     role="alert"
>
    <div class="flex">
        @if (! $hideIcon)
        <div class="py-1"><svg class="fill-current h-6 w-6 text-{{ $color }}-500 mr-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/></svg></div>
        @endif
        <div class="{{ $contentClasses ?? null }}">
            @if ($title)
                <p class="font-bold">{{ $title }}</p>
            @endif
            <p class="text-sm">{!! $slot !!}</p>
        </div>
    </div>
</div>
