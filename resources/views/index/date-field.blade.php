{!! $field->value ? now()->parse($field->value)->format($field->dateConfig['dateFormat']) : $field->value !!}
