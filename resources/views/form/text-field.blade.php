<x-move-form.row
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    :required="$field->isRequired()"
    help-text="{!! $field->gethelptext() !!}"
    :stacked="$field->stacked"
    :meta="$field->meta"
/>
