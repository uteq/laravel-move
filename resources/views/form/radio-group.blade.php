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
                x-data="{
                    active : {{ $field->value !== $value ? 'false' : 'true' }}
                }"
                x-on:click="active = true"
                class="
                    {{ $loop->first ? 'rounded-l-lg' : ' -ml-px' }}
                    {{ $loop->last ? 'rounded-r-lg' : '' }}
                    {{ $field->value === $value ? 'bg-white border-primary-500 drop-shadow' : 'shadow-inner hover:shadow-none bg-white text-gray-500 opacity-60 hover:border-primary-400 hover:opacity-100' }}
                    transition relative inline-flex items-center block border border-gray-300 shadow-sm px-4 py-1.5 cursor-pointer sm:flex sm:justify-between focus:z-10 focus:outline-none focus:ring-1 focus-within:ring-primary-900 focus:border-primary-900
                "
                <?php if ($field->value !== $value): ?> :class="{ 'shadow border-primary-500 shadow-none opacity-100 text-primary-500': active }" <?php endif; ?>
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
