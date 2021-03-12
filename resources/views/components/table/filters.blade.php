<div class="flex items-top justify-between flex-wrap sm:flex-no-wrap ml-2">
    <x-move-dropdown orientation="left" wire:key="table-filter.checkbox">
        <x-slot name="trigger">
            <button class="flex items-center cursor-pointer active:border relative mt-2" type="button">
                <span class="form-checkbox border rounded-md @if ($this->has_selected) bg-primary-600 @endif h-5 w-5 text-primary-600 cursor-pointer">
                    @if ($this->has_selected)
                        <!-- heroicon-s-check -->
                        <svg class="w-4 h-4 position-relative text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="left: 1px; top: 1px; position: relative">
                          <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    <svg class="absolute top-0 text-primary-600 h-4 w-4 ml-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </span>
            </button>
        </x-slot>

        <div class="px-3">
            <label class="inline-flex items-center mt-3">
                <x-move-field.checkbox name="selected" model="select_type.table" />
                <span class="ml-2 text-gray-700 text-sm">Alles selecteren</span>
            </label>
            <label class="inline-flex items-center my-3">
                <x-move-field.checkbox name="selected" model="select_type.all" />
                <span class="ml-2 text-gray-700 text-sm">Alle overeenkomende selecteren ({{ $this->total }})</span>
            </label>
        </div>
    </x-move-dropdown>

    <div class="flex items-center flex-wrap items-stretch">
        @if ($this->has_selected && count($this->actions() ?? []))
            <select class="form-control form-select mr-2 py-1" wire:model="action">
                <option disabled="disabled" selected="selected" value="-">Actie selecteren</option>
                <optgroup label="{{ $this->resource()->label() }}">
                @foreach($this->actions() as $i => $action)
                <option value="{{ \Str::slug($action->name) }}" wire:key="action.{{ $loop->index }}">{{ $action->name }}</option>
                @endforeach
                </optgroup>
            </select>

            @if ($this->action())
            <x-move-dialog-modal wire:model="showingAction">
                <x-slot name="button">
                    <button class="active:border @if ($this->action !== '-') bg-primary-600 hover:bg-primary-500 cursor-pointer @else bg-gray-200 disabled cursor-default @endif py-1 px-4 rounded-md mr-2"
                            x-on:click="show = true"
                            wire:loading.attr="disabled"
                            @if ($this->action === '-') disabled="disabled" @endif
                    >
                        <svg class="text-white w-6 h-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="title">
                     {{ $this->action()->name }}
                </x-slot>

                <x-slot name="content">
                    {{ $this->action()->render($this->resource()) }}
                </x-slot>

                <x-slot name="footer">
                    <x-move-secondary-button wire:click="$toggle('showingAction')" wire:loading.attr="disabled">
                        {{ $this->action()->cancelButtonText }}
                    </x-move-secondary-button>

                    <x-move-button class="ml-2" wire:click="handleAction" wire:loading.attr="disabled">
                        {{ $this->action()->confirmButtonText }}
                    </x-move-button>
                </x-slot>
            </x-move-dialog-modal>
            @endif
        @endif

        <x-move-dropdown orientation="right" wire:key="table-filter.filters">
            <x-slot name="trigger">
                <button type="button" class="flex items-center cursor-pointer active:border relative items-center cursor-pointer @if ($this->has_filters) bg-primary-600  hover:bg-primary-500 @else bg-gray-100 @endif py-1 pr-2 pl-1 rounded-md mr-2">
                    <svg class="@if ($this->has_filters) text-white @else text-gray-600 @endif  h-6 w-6 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                    @if ($this->has_filters) <span class="text-white px-2">{{ $this->has_filters }}</span> @endif
                    <svg class="@if ($this->has_filters) text-white @else text-gray-600 @endif h-4 w-4 ml-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </x-slot>

            {{ $slot }}
        </x-move-dropdown>

        @if ($this->has_selected)
            <x-move-dropdown orientation="right" wire:key="table-filter.delete">
                <x-slot name="trigger">
                    <button type="button" class="flex items-center cursor-pointer active:border relative items-center cursor-pointer bg-gray-100 py-1 pr-2 pl-1 rounded-md">
                        <svg class="text-gray-500 h-6 w-6" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Free 5.15.1 by @fontawesome  - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"></path></svg>
                        <div class="ml-2">{{ count($this->selected) }}</div>
                    </button>
                </x-slot>

                <button class="p-2" wire:click="showDelete">
                    Selectie verwijderen ({{ count($this->selected) }})
                </button>
            </x-move-dropdown>

            <x-move-dialog-modal wire:model="showingDelete">
                <x-slot name="title">
                    {{ count($this->selected) }} Item(s) verwijderen
                </x-slot>

                <x-slot name="content">
                    @lang('Are you sure you want to perform this action?')
                </x-slot>

                <x-slot name="footer">
                    <x-move-secondary-button wire:click="$toggle('showingDelete')" wire:loading.attr="disabled">
                        @lang('Cancel')
                    </x-move-secondary-button>

                    <x-move-button class="ml-2" wire:click="handleDelete" wire:loading.attr="disabled">
                        @lang('Perform action')
                    </x-move-button>
                </x-slot>
            </x-move-dialog-modal>
        @endif
    </div>
</div>
