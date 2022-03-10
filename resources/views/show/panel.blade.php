<x-move-panel
    :title="$panel->name"
    :panel="$panel"
    :afterTitle="$panel->afterTitle"
    :classes="$panel->classes ?? null"
>
    @foreach ($panel->fields as $key => $field)
    <x-move-row name="{{ $field->name() }}" class="px-6">
        {{ $field->render('show') }}
    </x-move-row>
    @endforeach

    @foreach ($panel->panels() as $subPanel)
        @if ($subPanel->empty()) @continue @endif

        @php $subPanel->alert = []; @endphp
        @php $subPanel->level = ($panel->level ?? 0) + 1; @endphp

        @if ($subPanel->level === 1)
            @php $subPanel->component = 'show.subpanel'; @endphp
{{--            @php $subPanel->class ??= 'px-4 mt-4 font-bold text-lg'; @endphp--}}
{{--            @php $subPanel->classes ??= 'grid grid-cols-6'; @endphp--}}
{{--            @php $subPanel->withoudCard = true; @endphp--}}
        @endif

        {{ $subPanel->render($model) }}
    @endforeach
</x-move-panel>
