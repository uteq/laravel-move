@foreach ($panels ?? [] as $subPanel)
    @if ($subPanel->empty()) @continue @endif

    {{ $subPanel->render($model) }}
@endforeach
