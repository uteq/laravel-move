<x-move-panel :title="$panel->name" :panel="$panel" :afterTitle="$panel->afterTitle">
    @foreach ($panel->fields as $key => $field)
        <x-move-row name="{{ $field->name() }}" class="px-4">
            {{ $field->render('show') }}
        </x-move-row>
    @endforeach

    @foreach ($panel->panels() as $subPanel)
        @php $subPanel->component = 'show.panel'; @endphp
        @php $subPanel->alert = []; @endphp
        @if ($subPanel->empty()) @continue @endif

        {{ $subPanel->render($model) }}
    @endforeach
</x-move-panel>
