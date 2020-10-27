<x-move-form.row model="{{ $field->model() }}"
            type="password"
            label="{{ $field->name }}"
            :required="$field->isRequired()"
            help-text="{{ $field->getHelpText() }}"
/>
