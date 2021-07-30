<x-move-form.row
    type="checkbox"
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    help-text="{{ $field->getHelpText() }}"
    :required="$field->isRequired()"
    :stacked="$field->stacked"
    :meta="$field->meta"
/>
