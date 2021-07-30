<x-move-form.row
    custom
    :stacked="$panel->meta['stacked'] ?? false"
    :meta="$panel->meta"
    :label="$panel->name"
    :model="$panel->id()"
    :flex="false"
>
    <div wire:key="move-{{ $panel->tableResource }}-{{ $this->model->id }}" class="flex-grow">
        <livewire:livewire.resource-table
            view="move.form-table"
            resource="{{ $panel->tableResource }}"
            :key="'move-{{ $panel->tableResource }}-' . $this->model->id"
            :parent-model="$this->model"
            :parent-resource-class="$this->resource()::class"
            :meta="$panel->meta"
{{--            :disable-delete-for="$panel->getDisableDeleteFor()"--}}
            :redirects="$panel->getRedirects()"
            :limit="$panel->meta['limit']"
        />
    </div>
</x-move-form.row>
