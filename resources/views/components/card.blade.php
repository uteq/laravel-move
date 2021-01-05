@props(['withPadding' => true, 'withoutPadding' => false, 'headerWithoutPadding' => false, 'shadow' => 'none', 'heading' => null, 'headingWithoutStyle' => false])

@php
    $defaultPadding = 'py-5 px-4';
    $padding = $withoutPadding ? '' : ($withPadding ? $defaultPadding : '');
@endphp

<div {{ $attributes->merge(['class' => 'border-b border-t border-gray-200 bg-white sm:border overflow-hidden sm:rounded-lg mb-6 ' . ($heading ? '' : $padding) .' shadow-' . $shadow]) }}>
    @if ($heading)
        <div class="bg-white border-b border-gray-200 {{ $headerWithoutPadding ? null : 'px-4 py-2' }}">
            @if (! $headingWithoutStyle)
            <div class="-ml-4 -mt-4 flex justify-between items-center flex-wrap sm:flex-nowrap">
                <div class="ml-4 mt-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
            @endif
                        {{ $heading }}
            @if (! $headingWithoutStyle)
                    </h3>
                </div>
            </div>
            @endif
        </div>
        <div class="divide-y divide-gray-200 {{ $withoutPadding ? null : $defaultPadding }}">
            {{ $slot }}
        </div>
    @else
        {{ $slot }}
    @endif
</div>
