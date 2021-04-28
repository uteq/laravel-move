<div class="grid gap-4 grid-cols-2">
    @foreach ($field->value as $key => $value)
        <div>{{ $key + 1 }}</div>
        <div>{{ $value }}</div>
    @endforeach
</div>
