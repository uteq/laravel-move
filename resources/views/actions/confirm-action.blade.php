@if ($fields && $resource)
    @foreach ($fields as $field)
        {{ $field->render() }}
    @endforeach
@else
    <p>{{ $action->confirmText }}</p>
@endif
