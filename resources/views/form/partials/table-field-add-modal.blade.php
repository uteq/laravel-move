<x-move-dialog-modal
    wire:model="showModal.{{ \Str::slug($this->resourceClass) }}"
    title="{{ __('Add :resource', ['resource' => $this->resourceClass::singularLabel()]) }}"
    closeable
>
    <livewire:livewire.resource-form
        wire:key="modal-form-{{ $showModal }}"
        name="modal-form-{{ $showModal }}"
        :resource="$this->resource"
        :model="$this->model"
        :redirects="$this->redirects"
        hide-actions
        action="create"
    />

    <x-slot name="footer">
        <div class="flex items-center justify-between">

            <x-move-button form="modal-form-{{ $showModal }}">
                {{ __('Create :resource', ['resource' => $this->resourceClass::singularLabel()]) }}
            </x-move-button>
        </div>
    </x-slot>
</x-move-dialog-modal>
