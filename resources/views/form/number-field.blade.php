<x-move-form.row
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    :required="$field->isRequired()"
    help-text="{{ $field->getHelpText() }}"
    type="number"
    step="{{ $field->step ?? '1' }}"
    :meta="$field->meta"
/>
