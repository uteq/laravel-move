@props(['title', 'content', 'id' => null, 'maxWidth' => null, 'button' => null, 'show' => true, 'withoutFooter' => false])

<x-move-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }} :show="$show"  :button="$button">
    <div class="px-6 py-4">
        <div class="text-lg">
            {{ $title }}
        </div>

        <div class="mt-4 break-words whitespace-wrap">
            {{ $content }}
        </div>
    </div>

    @if (! $withoutFooter)
    <div class="px-6 py-4 bg-gray-100 text-right">
        @if ($footer ?? null)
            {{ $footer }}
        @else
            <x-move-button x-on:click="show = false">
                {{ __('OK') }}
            </x-move-button>
        @endif
    </div>
    @endif
</x-move-modal>
