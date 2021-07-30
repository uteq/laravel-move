<x-move-form.row
    custom
    label="{{ $field->getName() }}"
    model="{{ $field->store }}"
    help-text="{{ $field->getHelpText() }}"
    :required="$field->isRequired()"
    :flex="false"
    :meta="$field->meta"
    :stacked="$field->stacked"
>
    <x-move-field.editor id="{{ $field->unique }}" wire:model="{{ $field->store }}" :value="$this->store" />
</x-move-form.row>
