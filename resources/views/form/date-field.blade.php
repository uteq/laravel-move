<x-move-form.row custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}" :stacked="$field->stacked">
    <x-move-field.date model="{{ $field->store }}" :config="$field->dateConfig" :required="$field->isRequired()" placeholder="{{ $field->placeholder }}" />
</x-move-form.row>

