@foreach ($this->panels() as $key => $panel)
    @if (! $panel->isShownOn('show', $panel->resource)) @continue @endif

    @if (! $hideCard && ! $panel->withoutCard)
        <div wire:key="move-main-panel-{{ $key }}">
            {{ $panel->render($model) }}
        </div>
    @else
        <div wire:key="move-main-panel-{{ $key }}">
            {{ $panel->render($model) }}
        </div>
    @endif
@endforeach

