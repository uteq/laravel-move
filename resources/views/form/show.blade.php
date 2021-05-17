<div>
    <h2 class="text-2xl font-semibold text-gray-900 mb-4 mt-6">
        {{ $panel->name }}
    </h2>

    <div wire:key="move-{{ $panel->showResource }}-{{ $this->model->id }}">
        <livewire:livewire.resource-show
            resource="{{ $panel->showResource }}"
            :key="'move-show-{{ $panel->showResource }}-' . $this->model->id"
            :model="$this->model"
            :model-id="$this->model->id"
            :hide-actions="$this->hideActions"
        />
    </div>
</div>
