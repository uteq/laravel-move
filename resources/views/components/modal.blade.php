@props(['id', 'maxWidth', 'button' => null, 'show' => true, 'showType' => '=='])

@php
$id = $id ?? md5($attributes->wire('model'));

switch ($maxWidth ?? '2xl') {
    case 'sm':
        $maxWidth = 'sm:max-w-sm';
        break;
    case 'md':
        $maxWidth = 'sm:max-w-md';
        break;
    case 'lg':
        $maxWidth = 'sm:max-w-lg';
        break;
    case 'xl':
        $maxWidth = 'sm:max-w-xl';
        break;
    case '2xl':
        $maxWidth = 'sm:max-w-2xl';
        break;
    case '3xl':
        $maxWidth = 'sm:max-w-3xl';
        break;
    case '4xl':
        $maxWidth = 'sm:max-w-4xl';
        break;
    default:
        $maxWidth = 'sm:max-w-xl';
        break;
}
@endphp

<div id="{{ $id }}" x-data="{ show: @entangle($attributes->wire('model')) }" class="z-10">

    @if ($button)
        {!! $button !!}
    @endif

    <div x-show="show {{ $showType }} {{ $show }}"
         x-on:close.stop="show = null"
         x-on:keydown.escape.window="show = null"
         class="fixed top-0 inset-x-0 px-4 py-6 sm:px-0 sm:flex sm:items-top sm:justify-center z-10 max-h-screen overflow-y-auto"
         style="display: none;"
    >
        <div class="fixed inset-0 transform transition-all z-10"
             x-on:click="show = null"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="z-index: 50;"
        ><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>

        <div class="bg-white rounded-lg overflow-y-auto shadow-xl transform transition-all sm:w-full {{ $maxWidth }}"
             style="z-index: 100;"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >{{ $slot }}</div>
    </div>
</div>
