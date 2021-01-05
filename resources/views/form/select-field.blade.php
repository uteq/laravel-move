@php $index = Str::slug(str_replace('.', '-', 'model.'. $field->attribute)) @endphp

<x-move-form.row
    custom
    label="{{ $field->name }}"
    model="{{ $field->store }}"
    :required="$field->isRequired()"
    help-text="{{ $field->getHelpText() }}"
>
    <div class="w-full text-black" x-data="{ form : false }">
        <x-move-field.select model="{{ $field->store }}"
                             placeholder="{{ $field->placeholder ?? null }}"
                             :settings="$field->settings"
                             :values="$field->store() ? array_keys(is_array($field->store()) ? $field->store() : [$field->store() => true]) : null"
        >
            @foreach ($field->getOptions() as $key => $value)
                <option value="{{ $key }}" @if ($key === $field->value) selected @endif >{{ $value }}</option>
            @endforeach
        </x-move-field.select>

        <div x-show="form" style="display: none">
            Hiiiiii
        </div>
    </div>
</x-move-form.row>

@push('scripts')
    <script>
        document.addEventListener("livewire:load", function () {

            Livewire.on('closeModal', function () {
            @this.set('showingAddResource.{{ $index }}', false);
            });

            Livewire.on('showingAddResource', function () {
                window.$('.select2-{{ $index }}').select2('close');
            });

            let $element = window.$('.select2-{{ $index }}');

            $element.on('select2:open', function (e) {
                window.$(".select2-dropdown:not(:has(a))").append(
                    '<a onclick="window.livewire.emit(\'showAddResource\', \'{{ $index }}\')"'
                    + 'style="padding: 6px;height: 20px;display: inline-table;"'
                    + 'class="text-center w-full text-{{ Move::getThemeColor() }}-500 text-bold cursor-pointer"'
                    + '>+ {{ $field->name }} toevoegen</a>'
                );
            });
        });
    </script>
@endpush
