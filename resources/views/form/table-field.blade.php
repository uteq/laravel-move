<span class="overflow-x-auto w-full">

    <x-move-table class="table-hover" wire:loading.class="opacity-50">

        <x-slot name="head">
            <tr>
                @foreach ($header as $field)
                    <x-move-th>
                        {!! $field->name() !!}
                    </x-move-th>
                @endforeach
                <x-move-th></x-move-th>
            </tr>
        </x-slot>

        <tbody wire:target="edit" wire:loading.remove>
        @forelse ($rows as $i => $row)
            <tr class="hover:bg-gray-50 bg-white shadow"
                wire:key="table-row-{{ $table->page ?? 0 }}-{{ $row['model']->id }}"
                wire:sortable.item="{{ $row['model']->id }}"
            >
                @foreach ($row['fields'] as $field)
                    @if ($field->isPlaceholder)
                        @continue
                    @endif

                    @php $field->applyResourceData($row['model']) @endphp

                    <x-move-td valign="top" class="{{ $field->shouldWrap() ? 'whitespace-wrap' : 'whitespace-nowrap' }}">
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
                <x-move-td valign="top">
                    <div class="flex gap-1">
                        @if ($meta['with_delete'] && ! $this->closure('disableDeleteFor', false, $row['model'], $row['fields'], $this))
                            @include('move::components.table.actions.delete', [
                                'table' => $this,
                                'id' => $row['model']->id,
                                'title' => $row['model']->{$table->resource()::$title},
                                'description' => $table->resource()->label(),
                            ])
                        @endif
                        @if ($meta['with_edit'] ?? true)
                            @include('move::form.partials.table-field-edit-modal', [
                                'model' => $row['model'],
                            ])
                        @endif
                    </div>
                </x-move-td>
            </tr>
        @empty
            <tr>
                <x-move-td class="hover:bg-gray-50 text-center" colspan="{{ count($header) + 2 }}">
                    <div class="p-10">
                        <p class="mb-3">@lang('No items available')</p>
                        @if ($this->meta['with_add_button'])
                        <button
                            type="button"
                            wire:click="$set('showModal', '{{ \Str::slug($this->resourceClass) }}')"
                            class="underline text-primary-500"
                        >
                            @lang('Form first :resource', ['resource' => $table->resource()->singularLabel()])
                        </button>
                        @endif
                    </div>
                </x-move-td>
            </tr>
        @endforelse
        </tbody>
    </x-move-table>

    @if ($collection instanceof Illuminate\Contracts\Pagination\LengthAwarePaginator)
        <div class="mt-5" wire:key="pagination">
            {{ $collection->withQueryString()->links('move::livewire.pagination.tailwind') }}
        </div>
    @endif

    @if ($this->meta['with_add_button'])
        <x-move-secondary-button wire:click="$set('showModal', '{{ \Str::slug($this->resourceClass) }}')" class="mt-2">
            {{ __('Create :resource', ['resource' => $this->resourceClass::singularLabel()]) }}
        </x-move-secondary-button>
    @endif

    @if ($showModal === \Str::slug($this->resourceClass))
        <div wire:key="move.table-field-add-modal-{{ \Str::slug($this->resourceClass) }}">
            @include('move::form.partials.table-field-add-modal')
        </div>
    @endif
</span>
