<div class="{{ $panel->class }}">
    <x-move-alert :color="$panel->color" :hideIcon="$panel->hideIcon" :customProperties="$panel->getProperties()">
        {!! $panel->getText($this, $this->store) !!}
    </x-move-alert>
</div>
