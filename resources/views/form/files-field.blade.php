<x-move-form.row width="w-full" custom label="{{ $field->name }}" model="{{ $field->model() }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">
    <div class="border p-2 bg-white mb-4 w-full">
        <input type="file"
               name="{{ $field->model() }}[]"
               wire:model="files.{{ $field->attribute }}"
               class="form-control cursor-pointer w-full"
               style="height: 44px"
               accept="image/*, .pdf, application/pdf, application/heic"
               capture="camera"
               multiple
        />

        <div wire:loading wire:target="files.{{ $field->attribute }}">
            Bestand wordt geüpload...
        </div>
    </div>

    @php $this->loadFiles($field) @endphp

    <x-slot name="append">
        <div class="flex flex-row flex-wrap">
        @if ($this->files)
            @foreach ($this->loadFiles($field) as $i => $file)

                @if (!$file instanceof \Uteq\Move\File\ResourceFileContract)
                    @continue
                @endif

                @if (!$file->exists())
                    @continue
                @endif

                <div class="w-full md:w-1/4 @if ($i % 1 === 0) pr-6 @endif " wire:key="file{{ $field->attribute }}{{ $loop->index }}{{ $i }}">
                    <div class="border p-3 rounded my-3">
                        <div class="flex justify-end">
                            <div class="mr-auto text-small" wire:target="rotateFile" wire:loading>
                                Draaien...
                            </div>
                            <div class="mr-auto text-small" wire:target="removeFile" wire:loading>
                                Laden...
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
