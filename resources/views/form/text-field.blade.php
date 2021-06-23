<x-move-form.row
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    :required="$field->isRequired()"
    help-text="{{ $field->getHelpText() }}"
    :stacked="$field->stacked"
    :meta="$field->meta"
/>
