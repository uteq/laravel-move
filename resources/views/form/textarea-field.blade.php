<x-move-form.row custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">
    <textarea wire:model="{{ $field->store }}"
              rows="{{ $field->rows }}"
              class="form-textarea w-full"
    ></textarea>
</x-move-form.row>
