<x-move-form.row model="{{ $field->store }}"
            type="password"
            label="{{ $field->name }}"
            :required="$field->isRequired()"
            help-text="{{ $field->getHelpText() }}"
/>
