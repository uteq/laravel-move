<div class="flex items-top justify-between flex-wrap sm:flex-no-wrap ml-2">

    <x-move-dropdown orientation="left" wire:key="table-filter.checkbox">
        <x-slot name="trigger">
            <button class="flex items-center cursor-pointer active:border relative mt-2">
                <span class="form-checkbox @if ($this->has_selected) bg-green-700 @endif h-5 w-5 text-green-600 cursor-pointer">
                    @if ($this->has_selected) <x-heroicon-s-check class="text-white" /> @endif
                </span>

                <x-heroicon-o-chevron-down class="text-gray-600 h-4 w-4 ml-1" />
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

    <div class="flex items-center flex-wrap">
        @if ($this->has_selected && count($this->actions() ?? []))
            <select class="form-control form-select mr-2 py-1" wire:model="action">
                <option disabled="disabled" selected="selected" value="-">Actie selecteren</option>
                <optgroup label="{{ $this->resource()->label() }}">
                @foreach($this->actions() as $i => $action)
                <option value="{{ \Str::slug($action->name) }}" wire:key="action.{{ $loop->index }}">{{ $action->name }}</option>
                @endforeach
                </optgroup>
            </select>

            <button class="active:border @if ($this->action !== '-') bg-green-600 hover:bg-green-500 cursor-pointer @else bg-gray-200 disabled cursor-default @endif py-1 px-4 rounded-md mr-2"
                    wire:click="showAction"
                    wire:loading.attr="disabled"
                    @if ($this->action === '-') disabled="disabled" @endif
            >
                <x-feathericon-play class="text-white w-6 h-6" />
            </button>

            @if ($this->action())
            <x-move-dialog-modal wire:model="showingAction">
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
                <button class="flex items-center cursor-pointer active:border relative items-center cursor-pointer @if ($this->has_filters) bg-green-600  hover:bg-green-500 @else bg-gray-100 @endif py-1 pr-2 pl-1 rounded-md mr-2">
                    <x-heroicon-o-filter class="@if ($this->has_filters) text-white @else text-gray-600 @endif h-6 w-6 ml-1" />
                    @if ($this->has_filters) <span class="text-white px-2">{{ $this->has_filters }}</span> @endif
                    <x-heroicon-o-chevron-down class="@if ($this->has_filters) text-white @else text-gray-600 @endif h-4 w-4 ml-1" />
                </button>
            </x-slot>

            {{ $slot }}
        </x-move-dropdown>

        @if ($this->has_selected)
            <x-move-dropdown orientation="right" wire:key="table-filter.delete">
                <x-slot name="trigger">
                    <button class="flex items-center cursor-pointer active:border relative items-center cursor-pointer bg-gray-100 py-1 pr-2 pl-1 rounded-md">
                        <x-fas-trash class="text-gray-500 h-6 w-6" />
                        <div class="ml-2">{{ count($this->selected) }}</div>
                        <x-heroicon-o-chevron-down class="text-gray-600 h-4 w-4 ml-1" />
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
                    {{ __('Weet u zeker dat u deze actie uit wilt voeren?') }}
                </x-slot>

                <x-slot name="footer">
                    <x-move-secondary-button wire:click="$toggle('showingDelete')" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-move-secondary-button>

                    <x-move-button class="ml-2" wire:click="handleDelete" wire:loading.attr="disabled">
                        {{ __('Voer actie uit') }}
                    </x-move-button>
                </x-slot>
            </x-move-dialog-modal>
        @endif
    </div>
</div>
