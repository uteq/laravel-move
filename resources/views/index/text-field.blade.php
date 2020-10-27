@if ($field->clickable)
    <a href="{{ $field->resourceUrl($this->resource()) }}" class="underline text-green-500">
        {{ $field->value }}
    </a>
@else
{{ $field->value }}
@endif
