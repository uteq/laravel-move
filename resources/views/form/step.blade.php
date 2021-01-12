@if ($panel->active())
<x-move-step :title="$panel->name" :panel="$panel" :hide-title="$panel->hideTitle">

    @foreach ($panel->fields as $key => $field)
        @if ($field->before)
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
        {{ $field->render($model->id ? 'edit' : 'create') }}
    @endforeach

    @foreach ($panel->panels() as $subPanel)
        @if ($subPanel->empty()) @continue @endif

        {{ $subPanel->render($model) }}
    @endforeach

    @if (($panel->next ?? null) && ! $this->model->id)
        <div class="flex justify-between mt-8">
            <a href="{{ $this->cancelRoute() }}" class="justify-self-left flex-grow pl-0 text-blue-600 hover:underline">
                {{ __('Cancel') }}
            </a>

            <div class="items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 active:bg-primary-900 focus:outline-none focus:border-primary-900 focus:shadow-outline-primary disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer"
                 wire:key="move-step-next-button-{{ $panel->name }}"
                 wire:click="validateStep('{{ $panel->name }}')"
            >{{ __('Next step') }}</div>
        </div>
    @endif

</x-move-step>
@endif