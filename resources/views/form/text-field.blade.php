<x-move-form.row
    model="{{ $field->store }}"
    label="{{ $field->name }}"
    :required="$field->isRequired()"
    help-text="{{ $field->getHelpText() }}"
/>
