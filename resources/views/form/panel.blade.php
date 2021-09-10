<x-move-panel
    :title="$panel->withoutTitle ? null : $panel->name"
    :panel="$panel"
    :classes="$panel->classes ?? null"
>
    @if ($panel->flow === 'col') <div class="flex"> @endif
        @foreach ($panel->fields as $key => $field)
            @if ($field->before ?? null)
                @php $before = $field->before @endphp
                <div class="pt-2 px-4 last:pb-4 bg-white w-full  grid grid-cols-6 gap-6">
                    <div class="col-span-6 sm:col-span-4">
                        <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
                            <div></div>
                            <div class="mt-1 sm:mt-0 sm:col-span-2">
                                {!! render($before($field, $model)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{ $field->render(is_string($model) ? $model : ($model->id ? 'edit' : 'create')) }}
        @endforeach

        @include('move::form.partials.render-panels', [
            'panels' => $panel->panels(),
            'model' => $model,
        ])
    @if ($panel->flow === 'col') </div> @endif
</x-move-panel>
