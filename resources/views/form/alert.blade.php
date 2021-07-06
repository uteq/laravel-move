<div class="p-4 sm:p-6">
    <x-move-alert :color="$panel->color">
        {{ $panel->getText($this->store) }}
    </x-move-alert>
</div>
