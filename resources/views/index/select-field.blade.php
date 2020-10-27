@if ($field->resourceName() && $field->showResourceUrl())
    <a href="{{ $field->showResourceUrl() }}" class="underline text-green-500">
        {{ $field->resourceName() }}
    </a>
@else
    {{ $field->value }}
@endif

