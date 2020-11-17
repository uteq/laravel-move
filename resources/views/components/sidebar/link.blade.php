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

<div x-data="{ open: {{ $open || $active ? 'true' : 'false' }} }">
    <a href="{{ $href }}"
       class="{{ $aClasses }}"
       @click="open = !open"
    >
        @if ($icon)
            <x-dynamic-component component="{{ $icon }}" class="mr-3 h-6 w-6 text-gray-200"/>
        @endif

        @if ($sub && $active && !$icon)
            <x-go-chevron-right-24 class="mr-5 h-4 w-4 text-green-200"/>
        @endif

        {{ $slot }}

        @if ($collapse)
            <x-dynamic-component component="heroicon-o-chevron-down"
                                 class="ml-auto mr-2 h-5 w-5 transform"
                                 style="display: none"
                                 x-show="open"
            ></x-dynamic-component>
            <x-dynamic-component component="heroicon-o-chevron-right"
                                 class="ml-auto mr-2 h-5 w-5 transform"
                                 x-show="!open"
            ></x-dynamic-component>
        @endif
    </a>

    @if ($collapse)
        <div class="mt-1 space-y-1 {{ $active ? 'block' : 'hidden' }}" :class="{'block': open, 'hidden': ! open}">
            {{ $collapse }}
        </div>
    @endif
</div>
