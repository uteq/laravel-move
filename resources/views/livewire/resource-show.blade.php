<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>{{ $this->resource()->singularLabel() }} details</div>
        </div>
    </x-slot>

    <div class="sm:absolute top-8 right:6 sm:right-8 md:right-10">
        <a wire:click="edit({{ $model->id }})" class="inline-flex cursor-pointer">
            <x-heroicon-s-pencil class="text-gray-400 hover:text-gray-600 h-6 w-6"/>
        </a>

        <a class="inline-flex cursor-pointer">
            <x-heroicon-o-trash class="text-gray-400 hover:text-gray-600 h-6 w-6" wire:click="confirmDestroy({{ $model->id }})"/>

            <x-move-dialog-modal wire:model="confirmingDestroy.{{ $model->id }}" wire:key="confirm.destroy">
                <x-slot name="title">
                    {{ __($this->resource()->singularLabel() . ' verwijderen') }}
                </x-slot>

                <x-slot name="content">
                    {{ __('Weet je zeker dat je deze ' . strtolower($this->resource()->singularLabel()) . ' wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.') }}
                </x-slot>

                <x-slot name="footer">
                    <x-move-secondary-button x-on:click="show = false" wire:click="hideConfirmDestroy({{ $model->id }})" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </x-move-secondary-button>

                    <x-move-button class="ml-2" wire:click="destroy({{ $model->id }})" wire:loading.attr="disabled">
                        {{ __('Handelaar verwijderen') }}
                    </x-move-button>
                </x-slot>
            </x-move-dialog-modal>
        </a>
    </div>

    <x-move-card class="mt-4">
        @foreach ($fields as $field)

            <x-move-row name="{{ $field->name() }}">
                {{ $field->render('show') }}
            </x-move-row>
        @endforeach
    </x-move-card>
</div>
