<x-move-form.row type="checkbox"
            model="{{ $field->store }}"
            label="{{ $field->name }}"
            help-text="{{ $field->getHelpText() }}"
            :required="$field->isRequired()"
/>
