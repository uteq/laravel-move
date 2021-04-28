<x-move-form.row custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">

    <div class="flex-col w-full">
        @foreach ($this->store[$field->attribute] ?? [] as $key => $value)
            <div class="flex items-center first:pt-0 pb-2">
                <div class="p-2">{{ $key + 1 }}</div>
                <div wire:key="{{ $field->name }}-{{ $key }}" class="flex-1">
                    <x-move-field.input wire:model="{{ $field->store }}.{{ $key }}" class="w-full" />
                </div>
                <div class="text-xs text-right text-primary-500 hover:underline"
                     wire:click="action('{{ $field->store }}', 'removeRow', '{{ $key }}')"
                >[x]</div>
            </div>
        @endforeach

        <div wire:click="action('{{ $field->store }}', 'addRow', '{{ count($this->store[$field->attribute] ?? []) - 1 }}')"
             class="text-sm text-gray-800 bg-white -bottom-3 border border-dashed max-w-md border-gray-300 mx-auto text-center cursor-pointer"
        >
            + Item toevoegen
        </div>
    </div>
</x-move-form.row>

