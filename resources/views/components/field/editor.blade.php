@props([
    'id',
    'theme' => 'snow',
    'version' => 1,
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
    'rows' => 5,
])

@php
    $value = is_array($value)
        ? Arr::get($value, Str::after($name, '.'))
        : $value;
@endphp

<div wire:ignore wire:key="{{ md5($version) }}" class="ql-editor-{{ $id }}-container">
    <div class="mt-2 bg-white">
        <div
            id="quillEditor{{ $id }}"
            x-data
            x-ref="quillEditor{{ $id }}"
            x-init="
                quill{{ $id }} = new Quill($refs.quillEditor{{ $id }}, {
                    theme: '{{ $theme }}',
                    placeholder: 'Typ je tekst hier...',
                    modules: {
                        toolbar: {{ json_encode($toolbar) }}
                    }
                });

                quill{{ $id }}.on('text-change', (delta, oldDelta, source) => {
                    // Get HTML content
                    $wire.set('{{ $name }}', unescape(encodeURIComponent(
                        $refs.quillEditor{{ $id }}.firstChild.innerHTML
                    )));
                });

                @if ($disableTab)
                    delete quill{{ $id }}.getModule('keyboard').bindings['9'];
                @endif
                "
            style="min-height: {{ $rows * 3 }}em; min-width: 100%;"
            class="w-full border rounded-b"
        >
            {!! $value !!}
        </div>
    </div>
    <style>
        .ql-editor-{{ $id }}-container .ql-editor {
            min-height: {{ $rows * 3 }}em;
        }

        @if (empty($toolbar))
            .ql-editor-{{ $id }}-container .ql-toolbar {
            display: none;
        }

        .ql-editor-{{ $id }}-container .ql-container {
            border-top: 1px solid #ccc !important;
            border-radius: 0.25rem;
        }
        @endif

        .ql-editor-{{ $id }}-container:hover .ql-container {
            border: 1px solid var(--tw-ring-color) !important;
            border-radius: 0.25rem;
        }
    </style>
</div>
<div class="hidden"></div>
