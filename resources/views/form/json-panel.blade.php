<x-move-panel :title="$panel->name" :panel="$panel" classes="flex-grow">

    @php

    $cols = [
        '1' => 'grid-cols-1',
        '2' => 'grid-cols-2',
        '3' => 'grid-cols-3',
        '4' => 'grid-cols-4',
        '5' => 'grid-cols-5',
        '6' => 'grid-cols-6',
        '7' => 'grid-cols-7',
        '8' => 'grid-cols-8',
        '9' => 'grid-cols-9',
        '10' => 'grid-cols-10',
        '11' => 'grid-cols-11',
        '12' => 'grid-cols-12',
    ][count($panel->fields)] ?? 'grid-cols-1';

    @endphp

    @if ($panel->formDisplayType == 'form')

        <div class="w-full">
            @if (($panel->meta['is_first'] ?? false) == true && ! ($panel->meta['hide_header'] ?? null))
            <div class="grid grid-flow-col {{ $cols }} border-b border-gray-100 py-4">
                @foreach ($panel->fields as $key => $field)

                    <div class="px-6 font-bold">{{ $field->name }}</div>

                @endforeach
            </div>
            @endif

            <div class="flex items-center">
                <div class="grid {{ $cols }} w-full">
                @foreach ($panel->fields as $key => $field)
                    <div wire:key="json-panel-{{ $panel->id }}-field-{{ $field->storePrefix }}-{{ $key }}">
                        {{ $field->render('edit') }}

                        <x-move-form.input-error for="{{ $field->store }}" class="mt-2"/>
                    </div>
                @endforeach
                </div>

                <button
                    type="button"
                    class="text-xs text-right text-gray-400 hover:text-primary-500 hover:underline cursor-pointer p-2"
                    wire:click="action('{{ $parentField->store }}', 'removeRow', '{{ $panelKey }}')"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </div>

    @else

     <div class="grid {{ $cols }} w-full text-sm gap-2">
         @if (($panel->meta['is_first'] ?? false) == true)
             @foreach ($panel->fields as $key => $field)
                <div class="font-bold">{{ $field->name }}</div>
             @endforeach
         @endif

         @foreach ($panel->fields as $key => $field)
             <div>{{ $field->store() }}</div>
         @endforeach
     </div>

    @endif

</x-move-panel>
