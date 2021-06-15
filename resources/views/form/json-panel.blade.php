<x-move-panel :title="$panel->name" :panel="$panel">
    <div class="lg:flex">
    @foreach ($panel->fields as $key => $field)
        <div wire:key="json-panel-{{ $panel->id }}-field-{{ $field->storePrefix }}-{{ $key }}">
            {{ $field->render('edit') }}

            <x-move-form.input-error for="{{ $field->store }}" class="mt-2"/>
        </div>
    @endforeach
    </div>
</x-move-panel>
