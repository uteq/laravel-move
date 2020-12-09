@props(['href', 'icon' => null, 'active' => false, 'open' => false, 'collapse' => null, 'sub' => false, 'altActive' => null])

@php
    $active = is_string($active) ? request()->is($active) : $active;
    $active = $altActive ? request()->is($altActive) : $active;
    $active = request()->url() === $href ? true : $active;
@endphp

@php
    $aClasses = $sub
        ? ($active
            ? 'group w-full flex items-center pl-2 pr-2 py-2 text-sm leading-5 font-medium bg-green-700 text-gray-200 rounded-md hover:text-white hover:bg-green-800 focus:outline-none focus:text-white focus:bg-green-800 transition ease-in-out duration-150'
            : 'group w-full flex items-center '. ($icon ? 'pl-2' : 'pl-11') .' pr-2 py-2 text-sm leading-5 font-medium text-gray-200 rounded-md hover:text-white hover:bg-green-700 focus:outline-none focus:text-white focus:bg-green-800 transition ease-in-out duration-150'
        )
        : ($active
            ? "mt-1 group w-full flex items-center pl-2 py-2 text-sm leading-5 font-medium text-white rounded-md bg-green-900 focus:outline-none focus:bg-green-800 transition ease-in-out duration-150"
            : "mt-1 group w-full flex items-center pl-2 pr-1 py-2 text-sm leading-5 font-medium rounded-md bg-green-500 text-gray-200 hover:text-white hover:bg-green-700 focus:outline-none focus:text-white focus:bg-green-800 transition ease-in-out duration-150"
        );
@endphp

<div x-data="{ open: {{ $active || $open ? 'true' : 'false' }} }">
    <a href="{{ $href }}"
       class="{{ $aClasses }}"
       @click="open = !open"
    >
        @if ($icon)
            {{ $icon }}
{{--            <x-dynamic-component component="{{ $icon }}" class="mr-3 h-6 w-6 text-gray-200"/>--}}
        @endif

        @if ($sub && $active && !$icon)
            <svg class="mr-5 h-4 w-4 text-green-200" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M8.72 18.78a.75.75 0 001.06 0l6.25-6.25a.75.75 0 000-1.06L9.78 5.22a.75.75 0 00-1.06 1.06L14.44 12l-5.72 5.72a.75.75 0 000 1.06z"></path>
            </svg>
        @endif

        {{ $slot }}

        @if ($collapse)
            <div x-show="open" class="ml-auto mr-2" style="display: none">
                <svg class="h-5 w-5 transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>

            <div x-show="!open" class="ml-auto mr-2">
                <svg class="h-5 w-5 transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        @endif
    </a>

    @if ($collapse)
        <div class="mt-1 space-y-1 {{ $active || $open ? 'block' : 'hidden' }}" :class="{'block': open, 'hidden': ! open}">
            {{ $collapse }}
        </div>
    @endif
</div>
