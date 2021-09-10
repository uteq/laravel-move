<div class="{{ $panel->containerClass }}">
    @if ($panel->meta['title'] ?? null)
        {{ $panel->meta['title'] }}
    @endif

    @foreach ($panel->fields as $key => $field)

        {{ $field->render($model->id ? 'edit' : 'create') }}

    @endforeach
</div>
