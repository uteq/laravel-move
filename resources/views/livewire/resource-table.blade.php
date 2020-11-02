<div>
    <x-move-table.header add-action="add"
                             add-text="Create {{ $this->resource()->singularLabel() }}"
                             search
    ></x-move-table.header>

    <x-move-table class="mt-4 table-hover">
        <x-slot name="filters">

            @if ($this->has_filters)
                <div class="uppercase underline cursor-pointer text-80 bg-30 p-1 text-center text-xs w-100 block"
                     wire:click="resetFilter">
                    Filters resetten
                </div>
            @endif

            @foreach ($this->filters() ?? [] as $key => $filter)
                <h3 class="text-sm text-gray-600 uppercase bg-gray-100 text-80 bg-30 p-2">
                    {{ $filter->name() }}
                </h3>
                <div class="p-3" wire:key="filter.{{ $key }}">
                    <x-dynamic-component :component="'filters.' . $filter->component()"
                                         :filter="$filter"
                    />
                </div>
            @endforeach

            <h3 class="text-sm text-gray-600 uppercase bg-gray-100 text-80 bg-30 p-2">
                Per pagina
            </h3>
            <div class="p-3">
                <select wire:model="filter.limit" class="block w-full form-control-sm form-select">
                    @foreach ($this->resource()::perPageOptions() as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </x-slot>

        <x-slot name="head">
            <tr>
                <x-move-th></x-move-th>
                @foreach ($header as $field)
                    <x-move-th>
                        @if ($field->sortable)
                            <div class="flex cursor-pointer" wire:click="sort('{{ $field->attribute }}')">
                                {{ $field->name() }}

                                <svg xmlns="http://www.w3.org/2000/svg"
                                     width="8"
                                     height="14"
                                     viewBox="0 0 8 14"
                                     class="ml-2 flex-no-shrink"
                                >
                                    <path
                                        d="M1.70710678 4.70710678c-.39052429.39052429-1.02368927.39052429-1.41421356 0-.3905243-.39052429-.3905243-1.02368927 0-1.41421356l3-3c.39052429-.3905243 1.02368927-.3905243 1.41421356 0l3 3c.39052429.39052429.39052429 1.02368927 0 1.41421356-.39052429.39052429-1.02368927.39052429-1.41421356 0L4 2.41421356 1.70710678 4.70710678z"
                                        class="fill-60 fill-current @if ($this->getSort($field->attribute) === 'asc') text-gray-600 @else text-gray-400 @endif "
                                    ></path>
                                    <path
                                        d="M6.29289322 9.29289322c.39052429-.39052429 1.02368927-.39052429 1.41421356 0 .39052429.39052429.39052429 1.02368928 0 1.41421358l-3 3c-.39052429.3905243-1.02368927.3905243-1.41421356 0l-3-3c-.3905243-.3905243-.3905243-1.02368929 0-1.41421358.3905243-.39052429 1.02368927-.39052429 1.41421356 0L4 11.5857864l2.29289322-2.29289318z"
                                        class="fill-60 fill-current @if ($this->getSort($field->attribute) === 'desc') text-gray-600 @else text-gray-400 @endif "></path>
                                </svg>
                            </div>
                        @else
                            {{ $field->name() }}
                        @endif
                    </x-move-th>
                @endforeach
                <x-move-th></x-move-th>
            </tr>
        </x-slot>

        <tbody wire:target="edit" wire:loading.remove>
        @forelse ($rows as $i => $row)
            <tr class="hover:bg-gray-50" wire:key="'table-row' . {{ $loop->index }}">
                <x-move-td>
                    <x-move-field.checkbox model="selected.{{ $row['model']->id }}"/>
                </x-move-td>
                @foreach ($row['fields'] as $field)
                    <x-move-td>
                        @if ($this->resource()::$title === $field->attribute)
                            <button wire:click="edit({{ $row['model']->id }})"
                                    class="text-green-500 cursor-pointer"
                                    wire:loading.attr="disabled"
                                    wire:loading.class="text-gray-500"
                            >
                                {{ $field->render('index') }}
                            </button>
                        @else
                            {{ $field->render('index') }}
                        @endif
                    </x-move-td>
                @endforeach
                <x-move-td>
                    <x-move-table.item-actions id="{{ $row['model']->id }}"
                                               description="{{ $this->resource()->label() }}"/>
                </x-move-td>
            </tr>
        @empty
            <tr>
                <x-move-td class="hover:bg-gray-50 text-center" colspan="{{ count($header) + 2 }}">
                    <div class="p-10">
                        <p class="mb-3">Geen items aanwezig</p>
                        <button wire:click="add" class="underline text-green-500">
                            Create first {{ $this->resource()->singularLabel() }}
                        </button>
                    </div>
                </x-move-td>
            </tr>
        @endforelse
        </tbody>

        <tr wire:target="edit" wire:loading>
            <x-move-td class="hover:bg-gray-50 text-center" colspan="{{ count($header) + 2 }}">
                Laden...
            </x-move-td>
        </tr>

    </x-move-table>

    @if ($this->action())
        <x-move-dialog-modal wire:model="hasError">
            <x-slot name="title">
                Error bij het uitvoeren van de actie {{ $this->action()->name }}.
            </x-slot>

            <x-slot name="content">
                {{ $this->error }}
            </x-slot>

            <x-slot name="footer">
                <x-move-button class="ml-2" wire:click="$toggle('hasError')" wire:loading.attr="disabled">
                    {{ __('OK') }}
                </x-move-button>
            </x-slot>
        </x-move-dialog-modal>
    @endif

    @if ($collection instanceof Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <div class="mt-5" wire:key="pagination">
            {{ $collection->withQueryString()->links() }}
        </div>
    @endif
</div>

@push('scripts')
    <script>
        window.livewire.on('_blank', path => {
            // open the download in a new tab/window to
            // prevent livewire component from freezing
            window.open(path, '_blank');
        });
    </script>
@endpush
