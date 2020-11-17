<x-move-panel :title="$panel->name" :panel="$panel">
    @foreach ($panel->fields as $key => $field)
        @if ($field->before)
            @php $before = $field->before @endphp
            <div class="pt-2 px-4 last:pb-4 bg-white w-full  grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-4">
                    <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
                        <div></div>
                        <div class="mt-1 sm:mt-0 sm:col-span-2">
                            {!! $before($field) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{ $field->render($model->id ? 'edit' : 'create') }}
    @endforeach
</x-move-panel>
