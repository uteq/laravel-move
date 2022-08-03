<div class="flex gap-2 items-center">
    @include('move::index.text.link')

    @if ($field->value)
    <a href="{{ $field->getExternalLink() }}"
       class="text-gray-400 hover:text-black"
       title="{{ __('Show example of :value', ['value' => $field->value]) }}"
       target="_blank"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
    </a>
    @endif
</div>
