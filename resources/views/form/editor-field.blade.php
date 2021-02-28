<x-move-form.row
    custom
    label="{{ $field->name }}"
    model="{{ $field->store }}"
    help-text="{{ $field->getHelpText() }}"
    :required="$field->isRequired()"
    :flex="false"
>
    <x-move-field.editor id="{{ $field->unique }}" wire:model="{{ $field->store }}" :value="$this->store" />
</x-move-form.row>
