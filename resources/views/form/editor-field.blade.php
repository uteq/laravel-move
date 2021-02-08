<x-move-form.row
    custom
    label="{{ $field->name }}"
    model="{{ $field->store }}"
    help-text="{{ $field->getHelpText() }}"
    :required="$field->isRequired()"
    :flex="false"
>
    <div wire:ignore>
        <div class="mt-2 bg-white">
            <div
                x-data
                x-ref="quillEditor{{ $field->unique }}"
                x-init="
                    quill{{ $field->unique }} = new Quill($refs.quillEditor{{ $field->unique }}, {
                        theme: 'snow',
                        bounds: document.body,
                        modules: {
                            toolbar: [
                              [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

                              ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
                              ['blockquote', 'code-block'],

                              [{ 'list': 'ordered'}, { 'list': 'bullet' }],


                              [{ 'align': [] }],

                              ['clean']                                         // remove formatting button
                            ]
                        }
                    });

                    quill{{ $field->unique }}.on('text-change', (delta, oldDelta, source) => {
                        // Get HTML content
                        $wire.set('{{ $field->store }}', unescape(encodeURIComponent(quill{{ $field->unique }}.root.innerHTML)));
                    });
                "
                style="min-height: 200px; min-width: 100%;"
                class="w-full border rounded-b"
            >
                <div class="ql-editor">{!! (\Illuminate\Support\Arr::get($this->store, \Illuminate\Support\Str::after($field->store, '.'))) !!}</div>
            </div>
        </div>
        <style>
            .ql-editor{
                min-height:200px;
            }
        </style>
    </div>
    <div class="hidden" ></div>
</x-move-form.row>
