<div class="text-right text-sm leading-5 font-medium">
    @if ($this->resource()->can('view'))
    <button wire:click="show({{ $id }})" class="inline-flex cursor-pointer">
        <x-heroicon-o-eye class="text-gray-400 hover:text-gray-600 h-6 w-6" />
    </button>
    @endif

    @if ($this->resource()->can('update'))
    <button wire:click="edit({{ $id }})" class="inline-flex cursor-pointer" wire:loading.attr="disabled">
        <x-heroicon-s-pencil class="text-gray-400 hover:text-gray-600 h-6 w-6"/>
    </button>
    @endif

    @if ($this->resource()->can('delete'))
    <a class="inline-flex cursor-pointer">

        <!-- Logout Other Devices Confirmation Modal -->
        <x-move-dialog-modal wire:model="confirmingDestroy" key="confirming.destroy.{{ $id }}">
            <x-slot name="button">
                    <x-heroicon-o-trash class="text-gray-400 hover:text-gray-600 h-6 w-6" @click="show = true"/>
            </x-slot>

            <x-slot name="title">@lang('Delete :resource', ['resource' => $description])</x-slot>

            <x-slot name="content">
                {{ $id }}
                @lang('Are you sure you want to remove this :resource? This action cannot be undone.', ['resource' => $description])
            </x-slot>

            <x-slot name="footer">
                <x-move-secondary-button
                    x-on:click="show = false"
                    wire:loading.attr="disabled"
                >@lang('Cancel')</x-move-secondary-button>

                <x-move-button
                    class="ml-2"
                    wire:click="destroy({{ $id }})"
                    wire:loading.attr="disabled"
                >@lang('Delete :resource', ['resource' => $this->resource()->singularLabel()])</x-move-button>
            </x-slot>
        </x-move-dialog-modal>
    </a>
    @endif
</div>
