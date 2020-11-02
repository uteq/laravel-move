<x-move-form.row custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">
    <x-move-field.date model="{{ $field->store }}" :config="$field->dateConfig" :required="$field->isRequired()" />
</x-move-form.row>

