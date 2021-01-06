@if ($field->clickable)
    <a href="{{ $field->resourceUrl($resource) }}" class="underline text-primary-500">
        {{ $field->value }}
    </a>
@else
{{ $field->value }}
@endif
