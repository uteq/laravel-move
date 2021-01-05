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
                x-ref="quillEditor"
                x-init="
                    quill = new Quill($refs.quillEditor, {
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
                    quill.on('selection-change', function () {
                        @this.set('{{ $field->store }}', quill.root.innerHTML)
                    });
                "
                style="min-height: 200px; min-width: 100%;"
                class="w-full border rounded-b"
            ></div>
        </div>
        <style>
            .ql-editor{
                min-height:200px;
            }
        </style>
    </div>
    <div class="hidden" ></div>
</x-move-form.row>
