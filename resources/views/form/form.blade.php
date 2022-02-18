<x-move-form.row
    custom
    :model="$panel->getUnique()"
    :label="$panel->withoutTitle ? null : $panel->getName()"
    :help-text="$panel->getHelpText()"
    :required="$panel->isRequired()"
    :meta="$panel->meta"
    :flex="$panel->flex"
    :stacked="$panel->stacked"
>
    <div class="flex-row" wire:key="form-panel-{{ $panel->getUnique() }}">
        @if ($panel && count($panel->alert))
            @foreach ($panel->alert as $type => $alerts)
                @foreach ($alerts as $alert)
                    <div class="bg-blue-50 border-t-4 border-blue-400 rounded-b text-blue-900 px-4 py-3 shadow-md" role="alert">
                        <p class="text-sm">{!! $alert !!}</p>
                    </div>
                @endforeach
            @endforeach
        @endif

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
