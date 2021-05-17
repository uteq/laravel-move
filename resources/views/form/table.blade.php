<div>
    <h2 class="text-2xl font-semibold text-gray-900 mb-4 mt-6">
        Ontdoeners
    </h2>

    <div wire:key="move-{{ $panel->tableResource }}-{{ $this->model->id }}">
        <livewire:livewire.resource-table
            resource="{{ $panel->tableResource }}"
            :key="'move-{{ $panel->tableResource }}-' . $this->model->id"
            :parent-model="$this->model"
            :parent-resource-class="$this->resource()::class"
        />
    </div>
</div>
