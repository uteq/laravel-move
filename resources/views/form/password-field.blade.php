<x-move-form.row
    model="{{ $field->store }}"
    type="password"
    label="{{ $field->getName() }}"
    :required="$field->isRequired()"
    help-text="{{ $field->getHelpText() }}"
    :meta="$field->meta"
/>
