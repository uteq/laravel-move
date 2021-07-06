<x-move-panel :title="$panel->name" :panel="$panel">

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
            @if (($panel->meta['is_first'] ?? false) == true)
            <div class="grid grid-flow-col {{ $cols }} border-b border-gray-100 py-4">
                @foreach ($panel->fields as $key => $field)

                    <div class="px-6 font-bold">{{ $field->name }}</div>

                @endforeach
            </div>
            @endif

            <div class="grid {{ $cols }} w-full">
            @foreach ($panel->fields as $key => $field)
                <div wire:key="json-panel-{{ $panel->id }}-field-{{ $field->storePrefix }}-{{ $key }}">
                    {{ $field->render('edit') }}

                    <x-move-form.input-error for="{{ $field->store }}" class="mt-2"/>
                </div>
            @endforeach
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
