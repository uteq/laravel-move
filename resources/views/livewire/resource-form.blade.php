<x-move-form-section submit="save" class="mt-5" wire:key="resource-form-{{ $this->model->id ?? rand(0, 99) }}">

    <x-slot name="form">
        @foreach ($this->panels() as $panel)
            @if ($panel->empty()) @continue @endif

            {{ $panel->render($model) }}
        @endforeach
    </x-slot>

    <x-slot name="actions">
        <x-move-action-message class="mr-3 text-primary-600" on="saved">
            <div class="flex">
                <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                @lang('Saved.')
            </div>
        </x-move-action-message>

        <x-move-a href="{{ $this->cancelRoute() }}">
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
