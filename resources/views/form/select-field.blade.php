@php $index = uniqid()  . '.' . Str::slug(str_replace('.', '-', 'model.'. $field->attribute)) @endphp

<x-move-form.row
    custom
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    help-text="{!! $field->gethelptext() !!}"
    :required="$field->isRequired()"
    :meta="$field->meta"
    :stacked="$field->stacked"
>
    <div class="w-full text-black" x-data="{ form : false }"
         wire:key="move-select-field-{{ $field->attribute }}-{{ $field->getVersion() }}"
    >
        <x-move-field.select
            model="{{ $field->store }}"
            placeholder="{{ $field->placeholder ?? null }}"
            :values="$field->values($this)"
            :options="$field->getOptions()"
            :settings="$field->settings"
            :multiple="$field->multiple"
        ></x-move-field.select>

        @if ($field->meta['with_add_button'])
            <x-move-secondary-button wire:click="$set('showModal', '{{ \Str::slug($field->resourceName) }}')" class="mt-2">
                {{ __('Form :resource', ['resource' => $field->resourceName()]) }}
            </x-move-secondary-button>

            @if ($this->showModal === \Str::slug($field->resourceName))
                <x-move-dialog-modal
                    wire:model="showModal.{{ \Str::slug($field->resourceName) }}"
                    title="{{ __('Form :resource', ['resource' => $field->resourceName()]) }}"
                >
                    <livewire:livewire.resource-form
                        wire:key="modal-form-{{ \Str::slug($field->resourceName) }}"
                        name="modal-form-{{ \Str::slug($field->resourceName) }}"
                        :resource="$field->resourceName"
                        :model="$field->resourceName::newModel()"
                        :redirects="$field->getRedirects()"
                        hide-actions
                    />

                    <x-slot name="footer">
                        <div class="flex items-center justify-between">
                            <x-move-secondary-button wire:click="$set('showModal', null)">
                                Annuleren
                            </x-move-secondary-button>

                            <x-move-button form="modal-form-{{ \Str::slug($field->resourceName) }}">
                                {{ __('Form :resource', ['resource' => $field->resourceName()]) }}
                            </x-move-button>
                        </div>
                    </x-slot>
                </x-move-dialog-modal>
            @endif
        @endif
    </div>
</x-move-form.row>

@push('scripts')
    <script>
        document.addEventListener("livewire:load", () => {
            moveLoadSelectField(
                '{{ $index }}',
                '{{ __('+ add :name', ['name' => $field->name]) }}'
            );
        });
    </script>
@endpush

@once
    <script wire:ignore>
        let moveLoadSelectField = function(index, addResourceText) {

            Livewire.on('closeModal', function () {
                @this.set('showingAddResource.' + index, false);
            });

            Livewire.on('showingAddResource', function () {
                window.$('.select2-' + index).select2('close');
            });

            let $element = window.$('.select2-' + index);

            $element.on('select2:open', function (e) {
                window.$(".select2-dropdown:not(:has(a))").append(
                    '<a onclick="window.livewire.emit(\'showAddResource\', index)"'
                    + 'style="padding: 6px;height: 20px;display: inline-table;"'
                    + 'class="text-center w-full text-primary-500 text-bold cursor-pointer"'
                    + '>' + addResourceText + '</a>'
                );
            });
        }
    </script>
@endonce
