<x-move-form.row model="{{ $field->model() }}"
            label="{{ $field->name }}"
            :required="$field->isRequired()"
            help-text="{{ $field->getHelpText() }}"
            type="number"
/>
