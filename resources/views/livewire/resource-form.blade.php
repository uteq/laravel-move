<x-move-form-section submit="save" class="mt-5" wire:key="resource-form-{{ $this->model->id ?? rand(0, 99) }}">

    <x-slot name="form">
        @foreach ($this->panels() as $panel)
            @if ($panel->empty()) @continue @endif
            <x-move-panel :title="$panel->name" :panel="$panel">
                @foreach ($panel->fields as $key => $field)
                    @if ($field->before)
                        @php $before = $field->before @endphp
                        <div class="pt-2 px-4 last:pb-4 bg-white w-full  grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-4">
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
                                    <div></div>
                                    <div class="mt-1 sm:mt-0 sm:col-span-2">
                                        {!! $before($field) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{ $field->render($model->id ? 'edit' : 'create') }}
                @endforeach
            </x-move-panel>
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
