<div class="whitespace-wrap">
    <x-move-dialog-modal
        wire:model="showModal.edit{{ $model['id'] }}"
        title="{{ __('Edit :resource', [
            'resource' => $this->resourceClass::singularLabel() . ' ' . \Uteq\Move\Facades\Move::resolveResource($this->resource)->title($model)
        ]) }}"
        closeable
    >
        <x-slot name="button">
            <svg class="text-gray-400 hover:text-gray-600 h-6 w-6 cursor-pointer" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
            </svg>
        </x-slot>

        @if ($showModal === 'edit' . $model['id'])
        <livewire:livewire.resource-form
            wire:key="modal-form-{{ $showModal }}-{{ $model['id'] }}"
            name="modal-form-{{ $showModal }}-{{ $model['id'] }}"
            :resource="$this->resource"
            :redirects="$this->redirects"
            :model="$model::class"
            :modelId="$model['id']"
            hide-actions
            action="edit"
        />
        @endif

        <x-slot name="footer">
            <div class="flex items-center justify-between">
                <x-move-button form="modal-form-{{ $showModal }}-{{ $model['id'] }}">
                    {{ __('Edit :resource', ['resource' => $this->resourceClass::singularLabel()]) }}
                </x-move-button>
            </div>
        </x-slot>
    </x-move-dialog-modal>
</div>
