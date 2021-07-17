<div class="{{ $panel->class }}">
    <x-move-alert :color="$panel->color">
        {{ $panel->getText($this->store) }}
    </x-move-alert>
</div>
