<x-move-form.row custom label="{{ $field->getName() }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}"  :meta="$field->meta">
    <textarea wire:model="{{ $field->store }}"
              rows="{{ $field->rows }}"
              class="w-full border rounded border-gray-300"
    ></textarea>
</x-move-form.row>
