<x-move-form-section submit="save" class="mt-10" wire:key="resource-form-{{ $this->model->id ?? rand(0, 99) }}">

    <x-slot name="form">

        @foreach ($fields as $key => $field)
            @if ($model)
                @php $field->resolveForDisplay($model->fill($modelData)) @endphp
            @endif

            {{ $field->type(isset($model->id) ? 'update' : 'create')->render() }}
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
            Annuleren
        </x-move-a>

        <x-move-button>
            @if ($model->id)
                {{ __( $this->label() . ' aanpassen') }}
            @else
                {{ __( $this->label() . ' aanmaken') }}
            @endif
        </x-move-button>
    </x-slot>
</x-move-form-section>
