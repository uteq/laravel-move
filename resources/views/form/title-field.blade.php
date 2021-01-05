<div>
    @if ($field->getHelpText())
        <div class="mt-2 help-text text-gray-500 text-sm">
            {!! $field->getHelpText() !!}
        </div>
    @endif

    <x-move-field.input model="{{ $field->store }}" placeholder="{{ __('Add a :label', ['label' => lcfirst($this->resource()->singularLabel()) . ' ' . lcfirst($field->name)]) }}" class="text-xl"/>

    <x-move-form.input-error for="{{ $field->store }}" class="mt-2"/>
</div>
