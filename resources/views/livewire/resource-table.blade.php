<div>
    <x-move-table.header
        add-action="{!! $this->addRoute() !!}"
        add-is-route
        :add-text="__('Form :resource', ['resource' => $this->resource()->singularLabel()])"
        search
    >
        @foreach ($headerSlots as $name => $value)
            <x-slot :name="$name">{!! $value !!}</x-slot>
        @endforeach
    </x-move-table.header>

    <x-move-table :table="$table" class="mt-4 table-hover" wire:loading.class="opacity-50">
        <x-slot name="filters">

            @if ($table->has_filters)
                <div class="uppercase underline cursor-pointer text-80 bg-30 p-1 text-center text-xs w-100 block"
                     wire:click="resetFilter">
                    @lang('Reset filters')
                </div>
            @endif

            @foreach ($table->filters() ?? [] as $key => $filter)
                <h3 class="text-sm text-gray-600 uppercase bg-gray-100 text-80 bg-30 p-2">
                    {{ $filter->name() }}
                </h3>
                <div class="p-3" wire:key="filter.{{ $key }}">
                    <x-dynamic-component
                        :component="'move::filters.' . $filter->component()"
                        :filter="$filter"
                    />
                </div>
            @endforeach

            <h3 class="text-sm text-gray-600 uppercase bg-gray-100 text-80 bg-30 p-2">
                @lang('Per page')
            </h3>
            <div class="p-3">
                <select wire:model="filter.limit" class="block w-full form-control-sm form-select rounded-md border-gray-300">
                    @foreach ($table->resource()::perPageOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot>

        <x-slot name="head">
            <tr>
                @if ($sortable)
                    <x-move-th></x-move-th>
                @endif
                <x-move-th></x-move-th>
                @foreach ($header as $field)
                    <x-move-th>
                        @if ($field->sortable)
                            <x-move-sortable :sort-order="$table->order[$field->attribute] ?? null" wire:click="sort('{{ $field->attribute }}')">
                                {!! $field->name() !!}
                            </x-move-sortable>
                        @else
                            {!! $field->name() !!}
                        @endif
                    </x-move-th>
                @endforeach
                <x-move-th></x-move-th>
            </tr>
        </x-slot>

        <tbody wire:target="edit" wire:loading.remove @if ($sortable) wire:sortable="updateOrder" @endif>
        @forelse ($rows as $i => $row)
            <tr class="hover:bg-gray-50 bg-white shadow" wire:key="table-row-{{ $table->page ?? 0 }}-{{ $row['model']->id }}" wire:sortable.item="{{ $row['model']->id }}">
                @if ($sortable)
                    <x-move-td wire:sortable.handle>
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <g>
                                <path fill="none" d="M0 0h24v24H0z"></path>
                                <path d="M12 22l-4-4h8l-4 4zm0-20l4 4H8l4-4zm0 12a2 2 0 1 1 0-4 2 2 0 0 1 0 4zM2 12l4-4v8l-4-4zm20 0l-4 4V8l4 4z"></path>
                            </g>
                        </svg>
                    </x-move-td>
                @endif
                <x-move-td>
                    <x-move-field.checkbox
                        model="selected.{{ $row['model']->id }}"
                        value="{{ $row['model']->id }}"
                        wire:key="selected-checkbox-{{ $row['model']->id }}"
                    />
                </x-move-td>
                @foreach ($row['fields'] as $field)
                    <x-move-td class="{{ Move::getWrapTableContent() && $field->wrapContent ? 'whitespace-wrap' : 'whitespace-nowrap' }}">
                        @if ($table->resource()::title($row['model']) === $field->attribute)
                            <button
                                wire:click="edit({{ $row['model']->id }})"
                                class="text-primary-500 cursor-pointer"
                                wire:loading.attr="disabled"
                                wire:loading.class="text-gray-500"
                            >
                                {!! $field->render('index') !!}
                            </button>
                        @else
                            {!! $field->render('index') !!}
                        @endif
                    </x-move-td>
                @endforeach
                <x-move-td class="whitespace-nowrap">
                    <x-move-table.item-actions
                        :table="$table"
                        id="{{ $row['model']->id }}"
                        description="{{ $table->resource()->label() }}"
                    />
                </x-move-td>
            </tr>
        @empty
            <tr>
                <x-move-td class="hover:bg-gray-50 text-center" colspan="{{ count($header) + 2 }}">
                    <div class="p-10">
                        <p class="mb-3">@lang('No items available')</p>
                        <a href="{{ $table->addRoute() }}" class="underline text-primary-500">
                            @lang('Form first :resource', ['resource' => $table->resource()->singularLabel()])
                        </a>
                    </div>
                </x-move-td>
            </tr>
        @endforelse
        </tbody>

        <tr wire:target="edit" wire:loading>
            <x-move-td class="hover:bg-gray-50 text-center" colspan="{{ count($header) + 2 }}">
                @lang('Loading...')
            </x-move-td>
        </tr>

    </x-move-table>

    @if ($table->action())
        <x-move-dialog-modal wire:model="hasError">
            <x-slot name="title">
                Error bij het uitvoeren van de actie {{ $table->action()->name }}.
            </x-slot>

            <x-slot name="content">
                {{ $table->error }}
            </x-slot>

            <x-slot name="footer">
                <x-move-button class="ml-2" wire:click="$toggle('hasError')" wire:loading.attr="disabled">
                    @lang('OK')
                </x-move-button>
            </x-slot>
        </x-move-dialog-modal>
    @endif

    @if ($collection instanceof Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <div class="mt-5" wire:key="pagination">
            {{ $collection->withQueryString()->links() }}
        </div>
    @endif

    <x-move-dialog-modal wire:model="showingActionResult" class="p-4">
        <x-slot name="title">
            {{ __('Response from action') }}
        </x-slot>
        <x-slot name="content">
            {!! $actionResult !!}
        </x-slot>
    </x-move-dialog-modal>
</div>
