<a class="inline-flex cursor-pointer" wire:key="a-confirming.destroy.{{ $id }}">

    <!-- Logout Other Devices Confirmation Modal -->
    <x-move-dialog-modal class="whitespace-wrap" wire:model="confirmingDestroy.{{ $id }}" wire:key="confirming.destroy.{{ $id }}" close>
        <x-slot name="button">
            <!-- heroicon-o-trash -->
            <svg class="text-gray-400 hover:text-gray-600 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
        </x-slot>

        <x-slot name="title">@lang('Delete :resource', ['resource' => $table->resource()->singularLabel()])</x-slot>

        <x-slot name="content">
            @lang('Are you sure you want to remove this :resource?<br /> This action cannot be undone.', ['resource' => $table->resource()->singularLabel() . ' ' . ($title ?? $id)])
        </x-slot>

        <x-slot name="footer">
            <x-move-button
                type="button"
                class="ml-2"
                wire:click="destroy('{{ $id }}')"
                wire:loading.attr="disabled"
            >@lang('Delete :resource', ['resource' => $table->resource()->singularLabel()])</x-move-button>
        </x-slot>
    </x-move-dialog-modal>
</a>
