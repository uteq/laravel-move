@props(['title', 'content' => null, 'id' => null, 'maxWidth' => null, 'button' => null, 'show' => true, 'withoutFooter' => false, 'close' => false])

<x-move-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }} :show="$show"  :button="$button">
    <div class="px-6 py-4 bg-white text-lg sticky w-full top-0">
        @if ($close)
            <div class="flex items-center justify-between">
                {!! $title !!}

                <span x-on:click="show = null">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
            </div>
        @else
            {!! $title !!}
        @endif
    </div>

    <div class="px-6 py-4 text-left {{ $withoutFooter ?: 'pb-16' }}">
        <div class="mt-4 break-words whitespace-wrap">
            {!! $content ?? $slot !!}
        </div>
    </div>

    @if (! $withoutFooter)
    <div class="px-6 py-4 bg-gray-100 text-right sticky w-full bottom-0">
        @if ($footer ?? null)
            {{ $footer }}
        @else
            <x-move-button type="button" x-on:click="show = false">
                {{ __('OK') }}
            </x-move-button>
        @endif
    </div>
    @endif
</x-move-modal>
