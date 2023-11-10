@props([
    'id',
    'theme' => 'snow',
    'version' => 1,
    'name' => optional($attributes->wire('model'))->value(),
    'value' => null,
    'disableTab' => false,
    'placeholder' => '',
    'toolbar' => [
        [[ 'header' => [1, 2, 3, 4, 5, 6, false] ]],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [[ 'list' => 'ordered'], [ 'list' => 'bullet' ]],
        [[ 'align' => [] ]],
        ['clean']
    ],
    'rows' => 5,
    'class' => 'w-full border rounded-b',
])

@php
    use Illuminate\Support\Arr;
    use Illuminate\Support\Str;

    $value = is_array($value) ? Arr::get($value, Str::after($name, '.')) : $value;
@endphp

<div wire:ignore wire:key="{{ md5($version) }}" class="ql-editor-{{ $id }}-container">
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
                    },
                    placeholder: '{{ $placeholder }}',
                });

                quill{{ $id }}.on('text-change', (delta, oldDelta, source) => {
                    // Get HTML content
                    $wire.set('{{ $name }}', $refs.quillEditor{{ $id }}.firstChild.innerHTML);
                });

                @if ($disableTab)
                    delete quill{{ $id }}.getModule('keyboard').bindings['9'];
                @endif
                "
            style="min-height: {{ $rows * 3 }}em; min-width: 100%;"
            class="{{ $class }}"
        >
            <div class="ql-editor-{{ $id }}" tabindex="1">{!! $value !!}</div>
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
                border-radius: 0.25rem !important;
            }
        @endif
    </style>
</div>
<div class="hidden" ></div>
