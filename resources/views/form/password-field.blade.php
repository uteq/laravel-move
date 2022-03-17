<x-move-form.row
    model="{{ $field->store }}"
    type="password"
    label="{{ $field->getName() }}"
    :required="$field->isRequired()"
    help-text="{!! $field->gethelptext() !!}"
    :meta="$field->meta"
    :stacked="$field->stacked"
/>
