<x-move-form.row
    custom
    model="{{ $field->store }}"
    label="{{ $field->getName() }}"
    help-text="{{ $field->getHelpText() }}"
    :required="$field->isRequired()"
    :meta="$field->meta"
    :stacked="$field->stacked"
    :flex="$field->flex"
>
    <fieldset id="move-radio-group-fieldset-{{ $field->store }}" class="flex">
        @foreach ($field->getOptions() as $value => $description)
            <label
                class="
                    {{ $loop->first ? 'rounded-l-lg' : 'border-l-0 -ml-px' }}
                    {{ $loop->last ? 'rounded-r-lg' : 'border-r-0' }}
                    {{ $field->value === $value ? 'bg-primary-100 text-primary-900' : 'bg-white hover:bg-gray-100' }}
                    relative inline-flex items-center block border border-gray-300 shadow-sm px-4 py-1.5 cursor-pointer hover:border-gray-400 sm:flex sm:justify-between focus:z-10 focus:outline-none focus:ring-1 focus-within:ring-primary-900 focus:border-primary-900
                "
                <?php if ($field->value !== $value): ?> :class="{ 'bg-gray-100': active }" <?php endif; ?>
                x-data="{ active : {{ $field->value !== $value ? 'false' : 'true' }} }"
                x-on:click="active = true"
            >
                <input
                    wire:model="{{ $field->store }}"
                    name="{{ $field->store }}"
                    type="radio"
                    value="{{ $value }}"
                    id="{{ $field->store . $value }}"
                    class="sr-only"
                />
                {{ $description }}
            </label>
        @endforeach
    </fieldset>
</x-move-form.row>
