@if ($field->clickable)
    <a href="{{ $field->resourceUrl($this->resource()) }}" class="underline text-primary-500">
        {{ $field->values($this)[$field->value] ?? $field->value }}
    </a>
@else
    {{ $field->values($this)[$field->value] ?? $field->value }}
@endif
