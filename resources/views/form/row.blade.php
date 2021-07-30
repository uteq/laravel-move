<div class="flex gap-4 mb-4 bg-white w-full">
    @foreach ($panel->fields as $key => $field)

        {{ $field->render($model->id ? 'edit' : 'create') }}

    @endforeach
</div>
