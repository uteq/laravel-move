@if ($field->resourceName() && $field->showResourceUrl())
    <a href="{{ $field->showResourceUrl() }}" class="underline text-primary-500">
        {{ $field->resourceName() }}
    </a>
@elseif ($field->resourceName())
    {{ $field->resourceName() }}
@else
    {{ $field->value }}
@endif
