@if ($field->clickable)
    <a href="{{ $field->resourceUrl($resource) }}" class="underline text-{{ move()::getThemeColor() }}-500">
        {{ $field->value }}
    </a>
@else
{{ $field->value }}
@endif
