<x-move-form.row type="checkbox"
            model="{{ $field->model() }}"
            label="{{ $field->name }}"
            help-text="{{ $field->getHelpText() }}"
            :required="$field->isRequired()"
/>
