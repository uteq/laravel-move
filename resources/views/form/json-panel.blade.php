<x-move-panel :title="$panel->name" :panel="$panel">
    <div class="lg:flex">
    @foreach ($panel->fields as $key => $field)
        <div wire:key="json-panel-{{ $panel->id }}-field-{{ $key }}">
            {{ $field->render('edit') }}
        </div>
    @endforeach
    </div>
</x-move-panel>
