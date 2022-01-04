<x-move-form.row
    custom
    label="{{ $field->name }}"
    model="{{ $field->store }}"
    help-text="{{ $field->getHelpText() }}"
    :required="$field->isRequired()"
    :flex="false"
    :meta="$field->meta"
    :stacked="$field->stacked"
    :placeholder="$field->placeholder"
>
    <x-move-field.editor
        id="{{ $field->unique }}"
        wire:model="{{ $field->store }}"
        :theme="$field->theme"
        :value="$field->value"
        :toolbar="$field->toolbar"
        :rows="$field->rows"
        :version="$field->getVersion()"
    />
</x-move-form.row>
