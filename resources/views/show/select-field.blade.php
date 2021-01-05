@if ($field->resourceName && $field->showResourceUrl())
    <a href="{{ $field->showResourceUrl() }}" class="underline text-{{ Move::getThemeColor() }}-500">
        {{ $field->resourceName() }}
    </a>
@else
    {{ $field->value }}
@endif

