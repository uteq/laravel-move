<x-move-form.row
    custom
    :stacked="$panel->meta['stacked'] ?? false"
    :meta="$panel->meta"
    :label="$panel->name"
    :model="$panel->id()"
    :flex="false"
>
    <div wire:key="move-{{ \Illuminate\Support\Str::slug($panel->tableResource) }}-{{ $this->model->getKey() }}" class="flex-grow">
        <livewire:livewire.resource-table
            view="move::form.table-field"
            resource="{{ $panel->tableResource }}"
            :wire:key="'move-'. \Illuminate\Support\Str::slug($panel->tableResource) .'-' . $this->model->getKey()"
            :parent-model="$this->model"
            :parent-resource-class="$this->resource()::class"
            :meta="$panel->meta"
            :disable-delete-for="$panel->getDisableDeleteFor()"
            :redirects="$panel->getRedirects()"
            :limit="$panel->meta['limit']"
            :show-fields="$panel->showFields"
            :hide-fields="$panel->hideFields"
        />
    </div>
</x-move-form.row>
