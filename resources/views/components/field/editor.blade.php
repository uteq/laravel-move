@props([
    'id',
    'theme' => 'snow',
    'name' => optional($attributes->wire('model'))->value(),
    'value' => null,
    'disableTab' => false,
    'toolbar' => [
        [[ 'header' => [1, 2, 3, 4, 5, 6, false] ]],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [[ 'list' => 'ordered'], [ 'list' => 'bullet' ]],
        [[ 'align' => [] ]],
        ['clean']
    ],
    'rows' => 5
])

<div wire:ignore>
    <div class="mt-2 bg-white">
        <div
            x-data
            x-ref="quillEditor{{ $id }}"
            x-init="
                quill{{ $id }} = new Quill($refs.quillEditor{{ $id }}, {
                    theme: '{{ $theme }}',
                    bounds: document.body,
                    modules: {
                        toolbar: {{ json_encode($toolbar) }}
                    }
                });

                quill{{ $id }}.on('text-change', (delta, oldDelta, source) => {
                    // Get HTML content
                    $wire.set('{{ $name }}', unescape(encodeURIComponent(quill{{ $id }}.root.innerHTML)));
                });

                @if ($disableTab)
                    delete quill{{ $id }}.getModule('keyboard').bindings['9'];
                @endif
                "
            style="min-height: {{ $rows * 16 }}em; min-width: 100%;"
            class="w-full border rounded-b"
        >
            <div class="ql-editor-{{ $id }}" tabindex="1">{!! (\Illuminate\Support\Arr::get($value, \Illuminate\Support\Str::after($name, '.'))) !!}</div>
        </div>
    </div>
    <style>
        .ql-editor-{{ $id }}{
            min-height: {{ $rows * 16 }}em;
        }
    </style>
</div>
<div class="hidden" ></div>
