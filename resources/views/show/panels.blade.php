@foreach ($this->panels() as $key => $panel)
    @if (! $panel->isShownOn('show', $panel->resource)) @continue @endif

    @if (! $hideCard && ! $panel->withoutCard)
        <x-move-card class="mt-4">
            <div wire:key="move-main-panel-{{ $key }}">
                {{ $panel->render($model) }}
            </div>
        </x-move-card>
    @else
        <div wire:key="move-main-panel-{{ $key }}">
            {{ $panel->render($model) }}
        </div>
    @endif
@endforeach

