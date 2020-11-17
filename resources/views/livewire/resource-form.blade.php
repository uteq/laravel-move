<x-move-form-section submit="save" class="mt-5" wire:key="resource-form-{{ $this->model->id ?? rand(0, 99) }}">

    <x-slot name="form">
        @foreach ($this->panels() as $panel)
            @if ($panel->empty()) @continue @endif

            {{ $panel->render($model) }}
        @endforeach
    </x-slot>

    <x-slot name="actions">
        <x-move-action-message class="mr-3 text-green-600" on="saved">
            <div class="flex">
                <x-heroicon-o-check-circle class="w-5 h-5 mr-2"/>
                @lang('Saved.')
            </div>
        </x-move-action-message>

        <x-move-a wire:click="cancel">
            Cancel
        </x-move-a>

        <x-move-button>
            @if ($model->id)
                @lang('Edit :resource', ['resource' => $this->label()])
            @else
                @lang('Create :resource', ['resource' => $this->label()])
            @endif
        </x-move-button>
    </x-slot>
</x-move-form-section>
