<x-move-form.row custom label="{{ $field->name }}" model="model.{{ $field->attribute }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">
    <textarea wire:model="{{ $field->model() }}"
              rows="{{ $field->rows }}"
              class="form-textarea w-full"
    ></textarea>
</x-move-form.row>
