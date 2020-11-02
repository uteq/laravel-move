<x-move-form-section submit="save" class="mt-5" wire:key="resource-form-{{ $this->model->id ?? rand(0, 99) }}">

    <x-slot name="form">
        @foreach ($this->panels() as $panel)
            @if ($panel->empty()) @continue @endif
            <x-move-panel :title="$panel->name">
                @foreach ($panel->fields as $key => $field)
                    {{ $field->render($model->id ? 'edit' : 'create') }}
                @endforeach
            </x-move-panel>
        @endforeach
    </x-slot>

    <x-slot name="actions">
        <x-move-action-message class="mr-3 text-green-600" on="saved">
            <div class="flex">
                <x-heroicon-o-check-circle class="w-5 h-5 mr-2"/>
                {{ __('Opgeslagen.') }}
            </div>
        </x-move-action-message>

        <x-move-a wire:click="cancel">
            Cancel
        </x-move-a>

        <x-move-button>
            @if ($model->id)
                {{ __('Edit ' . $this->label()) }}
            @else
                {{ __('Create ' . $this->label()) }}
            @endif
        </x-move-button>
    </x-slot>
</x-move-form-section>
