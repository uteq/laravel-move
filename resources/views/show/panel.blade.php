<div>

    @foreach ($panel->fields as $field)
        <x-move-row name="{{ $field->name() }}">
            {{ $field->render('show') }}
        </x-move-row>
    @endforeach

</div>
