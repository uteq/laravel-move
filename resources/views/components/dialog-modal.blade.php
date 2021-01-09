@props(['id' => null, 'maxWidth' => null, 'button' => null, 'show' => true])

<x-move-modal :id="$id" :maxWidth="$maxWidth" {{ $attributes }} :show="$show"  :button="$button">
    <div class="px-6 py-4">
        <div class="text-lg">
            {{ $title }}
        </div>

        <div class="mt-4 break-words whitespace-wrap">
            {{ $content }}
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-100 text-right">
        {{ $footer }}
    </div>
</x-move-modal>
