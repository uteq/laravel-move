<x-move-form.row
    custom
    :model="$panel->getUnique()"
    :label="$panel->getName()"
    :help-text="$panel->getHelpText()"
    :required="$panel->isRequired()"
    :meta="$panel->meta"
    :flex="$panel->flex"
>

    <div class="flex-row" wire:key="form-panel-{{ $panel->getUnique() }}">
        @foreach ($panel->fields as $key => $field)
            <div class="mb-4" wire:key="form-panel-{{ $panel->getUnique() }}-field-{{ $field->storePrefix ?? null }}-{{ $key }}">
                {{ $field->render($model->id ? 'edit' : 'create') }}
            </div>

        @endforeach

        @include('move::form.partials.render-panels', [
            'panels' => $panel->panels(),
            'model' => $model,
        ])
    </div>

</x-move-form.row>
