<x-move-form.row
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    :required="$field->isRequired()"
    help-text="{!! $field->gethelptext() !!}"
    type="number"
    step="{{ $field->step ?? '1' }}"
    :meta="$field->meta"
    :stacked="$field->stacked"
/>
