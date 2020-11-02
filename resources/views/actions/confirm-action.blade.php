@if ($fields && $resource)
    @foreach ($fields as $field)
        {{ $field->render('create') }}
    @endforeach
@else
    <p>{{ $action->confirmText }}</p>
@endif
