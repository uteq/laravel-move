<x-move-form.row width="w-full" custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">

    <label class="w-full">
        <div class="flex text-center items-center px-4 py-2 bg-white text-blue rounded-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-teal-800 hover:text-white">
            <x-heroicon-o-cloud-download class="w6 h-6" />
            <span class="ml-2 mt-1 text-base leading-normal">
                @lang('Add a new file')
            </span>
        </div>
        <input type="file"
               name="{{ $field->store }}[]"
               class="hidden"
               wire:model="files.{{ $field->attribute }}"
               accept="image/*, .pdf, application/pdf, application/heic"
               capture="camera"
               multiple
        />

        <div wire:loading wire:target="files.{{ $field->attribute }}">
            @lang('File is being uploaded')
        </div>

    </label>

    @php $this->loadFiles($field) @endphp

    <x-slot name="append">
        <div class="grid grid-cols-3 gap-4">
        @if ($this->files)
            @foreach ($this->loadFiles($field) as $i => $file)

                @if (!$file instanceof \Uteq\Move\File\ResourceFileContract)
                    @continue
                @endif

                @if (!$file->exists())
                    @continue
                @endif

                <div wire:key="file{{ $field->attribute }}{{ $loop->index }}{{ $i }}">
                    <div class="border p-3 rounded my-3">
                        <div class="flex justify-end">
                            <div class="mr-auto text-small" wire:target="rotateFile" wire:loading>
                                @lang('Turning...')
                            </div>
                            <div class="mr-auto text-small" wire:target="removeFile" wire:loading>
                                @lang('Loading...')
                            </div>

                            <a wire:click="rotateFile('{{ $field->attribute }}', {{ $i }})" class="cursor-pointer mr-2">
                                <x-feathericon-rotate-cw class="text-gray-400 hover:text-gray-600 h-6 w-6" />
                            </a>

                            <a wire:click="removeFile('{{ $field->attribute }}', {{ $i }})" class="cursor-pointer">
                                <x-heroicon-o-trash class="text-gray-400 hover:text-gray-600 h-6 w-6"/>
                            </a>
                        </div>

                        @if ($file->guessExtension() === 'pdf')

                            <div class="card-body">

                                <a href="{{ $file->getUrl() }}"
                                   target="_blank"
                                   class="btn-group btn-group-sm w-100"
                                >
                                    <div class="btn btn-outline-secondary small">
                                        <x-far-file-pdf class="w-6 h-6" />
                                        {{ $file->getClientOriginalName() }}
                                    </div>
                                    <div class="btn btn-secondary">
                                        <x-heroicon-o-external-link class="w-6 h-6" />
                                    </div>
                                </a>
                            </div>

                        @else
                            <div class="col p-2 justify-content-center">
                                <x-move-modal id="showingFile{{ $i }}" wire:model="showFile">
                                    <x-slot name="button">
                                        <div @click="show = true" class="cursor-pointer" data-lightbox="roadtrip">
                                            <img src="{{ $file->withVersion($this->rotatedFiles)->getUrl() }}"
                                                 class="card-img object-center"
                                            />
                                        </div>
                                    </x-slot>

                                    <img src="{{ $file->withVersion($this->rotatedFiles)->getUrl() }}"
                                         class="card-img"
                                    />
                                </x-move-modal>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
        </div>
    </x-slot>

</x-move-form.row>
