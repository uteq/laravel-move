<x-move-form.row stacked custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">

    <div class="flex-col w-full">
        <div class="mb-2">
        @foreach ($this->store[$field->attribute] ?? [] as $key => $value)
            @if ($field->fields)
            <div wire:key="json-panel-{{ $field->attribute }}-{{ $key }}" class="my-1">
                <div class="flex items-center">
                    {{ $field->panel($this, $key) }}

                    <button type="button" class="text-xs text-right text-gray-400 hover:text-primary-500 hover:underline cursor-pointer p-2"
                         wire:click="action('{{ $field->store }}', 'removeRow', '{{ $key }}')"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            </div>
            @else
            <div class="flex items-center first:pt-0 pb-2">
                <div class="p-2">{{ $key + 1 }}</div>
                <div wire:key="{{ $field->name }}-{{ $key }}" class="flex-1">
                    <x-move-field.input wire:model="{{ $field->store }}.{{ $key }}" class="w-full" />
                </div>
                <div class="text-xs text-right text-primary-500 hover:underline"
                     wire:click="action('{{ $field->store }}', 'removeRow', '{{ $key }}')"
                >[x]</div>
            </div>
            @endif
        @endforeach
        </div>

        <div wire:click="action('{{ $field->store }}', 'addRow', '{{ count($this->store[$field->attribute] ?? []) - 1 }}')"
             class="text-sm text-gray-800 bg-white -bottom-3 border border-dashed max-w-md border-gray-300 hover:border-primary-300 hover:text-primary-500 hover:underline mx-auto text-center cursor-pointer rounded"
        >
            {{ $field->addItemText }}
        </div>
    </div>
</x-move-form.row>

