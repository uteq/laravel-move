@php $index = Str::slug(str_replace('.', '-', 'model.'. $field->attribute)) @endphp

<x-move-form.row
    custom
    :required="$field->isRequired()"
    label="{{ $field->getName() }}"
    model="{{ $field->store }}"
    help-text="{{ $field->getHelpText() }}"
    :meta="$field->meta"
>
    <div class="w-full text-black" x-data="{ form : false }" wire:key="move-select-field-{{ $field->attribute }}-{{ $field->getVersion() }}">
        <x-move-field.select
            model="{{ $field->store }}"
            placeholder="{{ $field->placeholder ?? null }}"
            :values="$field->values($this)"
            :options="$field->getOptions()"
            :settings="$field->settings"
            :multiple="$field->multiple"
        ></x-move-field.select>
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
