@if ($field->clickable)
    <a href="{{ $field->resourceUrl($this->resource()) }}" class="underline text-{{ move()::getThemeColor() }}-500">
        {{ $field->value }}
    </a>
@else
    {{ $field->value }}
@endif
