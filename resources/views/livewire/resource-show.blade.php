<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>{{ $this->resource()->singularLabel() }} details</div>
        </div>
    </x-slot>

    <div class="sm:absolute top-8 right:6 sm:right-8 md:right-10">
        @if ($this->resource()->can('update'))
        <a wire:click="edit('{{ $model->id }}')" class="inline-flex cursor-pointer">
            <!-- heroicon-s-pencil -->
            <svg class="text-gray-400 hover:text-gray-600 h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
            </svg>
        </a>
        @endif

        @if ($this->resource()->can('delete'))
        <a class="inline-flex cursor-pointer">
            <x-move-dialog-modal wire:model="confirmingDestroy" wire:key="confirm.destroy">
                <x-slot name="button">
                    <div x-on:click="show = true">
                        <!-- heroicon-o-trash -->
                        <svg class="text-gray-400 hover:text-gray-600 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </div>
                </x-slot>

                <x-slot name="title">
                    @lang('Delete :resource', ['resource' => $this->resource()->singularLabel()])
                </x-slot>

                <x-slot name="content">
                    @lang('Are you sure you want to remove this :resource? This action cannot be undone.', ['resource' => strtolower($this->resource()->singularLabel())])
                </x-slot>

                <x-slot name="footer">
                    <x-move-secondary-button x-on:click="show = false" wire:click="hideConfirmDestroy('{{ $model->id }}')" wire:loading.attr="disabled">
                        @lang('Cancel')
                    </x-move-secondary-button>

                    <x-move-button class="ml-2" wire:click="destroy('{{ $model->id }}')" wire:loading.attr="disabled">
                        @lang('Delete :resource', ['resource' => $this->resource()->singularLabel()])
                    </x-move-button>
                </x-slot>
            </x-move-dialog-modal>
        </a>
        @endif
    </div>

    <x-move-card class="mt-4">
        @foreach ($fields as $field)
            <x-move-row name="{{ $field->name() }}">
                {{ $field->render('show') }}
            </x-move-row>
        @endforeach
    </x-move-card>
</div>
