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
        [ 'link', 'image', 'video', 'formula' ],
        ['clean']
    ],
    'rows' => 5,
    'placeholder' => __('Typ je tekst hier...'),
    'settings' => [],
])

@php
    $value = is_array($value)
        ? Arr::get($value, Str::after($name, '.'))
        : $value;

    $settings = array_replace_recursive($settings, [
        'theme' => $theme,
        'placeholder' => $placeholder,
        'modules' => [
            'toolbar' => $toolbar,
        ],
    ]);
@endphp

<div wire:ignore wire:key="{{ md5($version) }}" class="ql-editor-{{ $id }}-container">
    <div class="mt-2 bg-white">
        <div
            id="quillEditor{{ $id }}"
            x-data
            x-ref="quillEditor{{ $id }}"
            x-init="
                quill{{ $id }} = new Quill(
                    $refs.quillEditor{{ $id }},
                    {{ json_encode($settings) }}
                );

                quill{{ $id }}.on('text-change', (delta, oldDelta, source) => {
                    // Get HTML content
                    $wire.set('{{ $name }}', $refs.quillEditor{{ $id }}.firstChild.innerHTML);
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

        .ql-editor-{{ $id }}-container p {
            padding-bottom: 20px;
        }

        .ql-editor-{{ $id }}-container p:last-child {
            padding-bottom: 0;
        }

        @if (empty($toolbar))
            .ql-editor-{{ $id }}-container .ql-toolbar {
            display: none;
        }

        .ql-editor-{{ $id }}-container .ql-container {
            border-top: 1px solid #ccc !important;
            border-radius: 0.25rem;
        }

        .ql-editor-{{ $id }}-container:hover .ql-container {
            border: 1px solid var(--tw-ring-color) !important;
            border-radius: 0.25rem;
        }
        @endif
    </style>
</div>
<div class="hidden"></div>
