@if ($field->clickable)
    <a href="{{ $field->resourceUrl($this->resource()) }}" class="underline text-{{ Move::getThemeColor() }}-500">
        {{ $field->value }}
    </a>
@else
    {{ $field->value }}
@endif
