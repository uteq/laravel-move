@props(['id', 'maxWidth', 'button' => null, 'value' => null, 'show' => true, 'showType' => '=='])

@php
$model = $attributes->wire('model');

$parts = explode('.', $model);
$model = (count($parts) > 1)
    ? $parts[0]
    : $model;

if (!$value && count($parts) > 1) {
    unset($parts[0]);

    $value = implode('.', $parts);
}

if ($value) {
    $showType = '===';
    $show = "'". (string) $value ."'";
}

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
    case '5xl':
        $maxWidth = 'sm:max-w-5xl';
        break;
    case '6xl':
        $maxWidth = 'sm:max-w-6xl';
        break;
    default:
        $maxWidth = 'sm:max-w-xl';
        break;
}
@endphp

<div
    id="{{ $id }}"
    x-data="{
        show: @entangle($model),
        focusables() {
            // All focusable element types...
            let selector = 'a, button, input, textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'

            return [...$el.querySelectorAll(selector)]
                // All non-disabled elements...
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    {{ $attributes->wire('key') }}
>

    @if ($button)
        @if ($value)
            <div x-on:click="show = {{ $show }}">
                {!! $button !!}
            </div>
        @else
            <div x-on:click="show = true">
                {!! $button !!}
            </div>
        @endif
    @endif

    <div x-show="show {{ $showType }} {{ $show }}"
         x-on:close.stop="show = null"
         x-on:keydown.escape.window="show = null"
         class="fixed top-0 inset-x-0 px-4 py-6 sm:px-0 sm:flex sm:items-top sm:justify-center z-50 max-h-screen overflow-y-auto"
         style="display: none;"
    >
        <div class="fixed inset-0 transform transition-all"
             x-on:click="show = null"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
        ><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>

        <div class="bg-white rounded-lg overflow-y-auto shadow-xl transform transition-all sm:w-full {{ $maxWidth }}"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >{{ $slot }}</div>
    </div>
</div>
