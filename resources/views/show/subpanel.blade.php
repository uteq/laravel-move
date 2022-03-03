<x-move-row
    name="{{ $panel->name }}"
    class="px-4"
>
    <div class="flex flex-col gap-4">
        @foreach ($panel->fields as $key => $field)
            <div class="flex flex-col gap-2">
                <div class="opacity-60">{{ $field->name() }}</div>
                <div>{{ $field->render('show') }}</div>
            </div>
        @endforeach
    </div>
</x-move-row>
