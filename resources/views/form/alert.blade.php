<div class="{{ $panel->class }}">
    <x-move-alert :color="$panel->color" :hideIcon="$panel->hideIcon">
        {!! $panel->getText($this, $this->store) !!}
    </x-move-alert>
</div>
