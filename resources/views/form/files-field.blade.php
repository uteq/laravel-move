<x-move-form.row width="w-full" custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">

    <label class="w-full">
        <div class="flex text-center items-center px-4 py-2 bg-white text-blue rounded-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-teal-800 hover:text-white">
            <!-- heroicon-o-cloud-download -->
            <svg class="w6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
            </svg>
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
               {{ $field->isMultiple ? 'multiple' : null }}
        />

        <div wire:loading wire:target="files.{{ $field->attribute }}">
            @lang('File is being uploaded')
        </div>

    </label>

    @php $this->loadFiles($field) @endphp

    <x-slot name="append">
        <div class="grid grid-cols-1 gap-4 mt-3">
        @if ($this->files)
            @foreach ($this->loadFiles($field) as $i => $file)

                @if (!$file instanceof \Uteq\Move\File\ResourceFileContract)
                    @continue
                @endif

                @if (!$file->exists())
                    @continue
                @endif

                <div wire:key="file{{ $field->attribute }}{{ $loop->index }}{{ $i }}" class="border rounded ">
                    <div class="flex">

                        <div>
                        @if ($file->guessExtension() === 'pdf')

                            <div class="card-body">

                                <a href="{{ $file->getUrl() }}"
                                   target="_blank"
                                   class="flex"
                                >
                                    <div class="flex small m-3">
                                        <!-- far-file-pdf -->
                                        <svg class="w-6 h-6" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><!-- Font Awesome Free 5.15.1 by @fontawesome  - https://fontawesome.com License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License) --><path d="M369.9 97.9L286 14C277 5 264.8-.1 252.1-.1H48C21.5 0 0 21.5 0 48v416c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48V131.9c0-12.7-5.1-25-14.1-34zM332.1 128H256V51.9l76.1 76.1zM48 464V48h160v104c0 13.3 10.7 24 24 24h104v288H48zm250.2-143.7c-12.2-12-47-8.7-64.4-6.5-17.2-10.5-28.7-25-36.8-46.3 3.9-16.1 10.1-40.6 5.4-56-4.2-26.2-37.8-23.6-42.6-5.9-4.4 16.1-.4 38.5 7 67.1-10 23.9-24.9 56-35.4 74.4-20 10.3-47 26.2-51 46.2-3.3 15.8 26 55.2 76.1-31.2 22.4-7.4 46.8-16.5 68.4-20.1 18.9 10.2 41 17 55.8 17 25.5 0 28-28.2 17.5-38.7zm-198.1 77.8c5.1-13.7 24.5-29.5 30.4-35-19 30.3-30.4 35.7-30.4 35zm81.6-190.6c7.4 0 6.7 32.1 1.8 40.8-4.4-13.9-4.3-40.8-1.8-40.8zm-24.4 136.6c9.7-16.9 18-37 24.7-54.7 8.3 15.1 18.9 27.2 30.1 35.5-20.8 4.3-38.9 13.1-54.8 19.2zm131.6-5s-5 6-37.3-7.8c35.1-2.6 40.9 5.4 37.3 7.8z"></path></svg>
                                        <div class="ml-2 text-sm">
                                            {{ $file->getClientOriginalName() }}
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <!-- heroicon-o-external-link -->
                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </div>
                                </a>
                            </div>

                        @else
                            <x-move-modal id="showingFile{{ $i }}" show="{{ $i }}" show-type="===" wire:model="showFile">
                                <x-slot name="button">
                                    <div @click="show = {{ $i }}" class="cursor-pointer flex" data-lightbox="roadtrip">
                                        <img src="{{ $file->withVersion($this->rotatedFiles)->getUrl() }}"
                                             class="w-1/3 h-auto object-contain object-left"
                                        />
                                        <div class="ml-4 text-sm">{{ $file->getClientOriginalName() }}</div>
                                    </div>
                                </x-slot>

                                <img src="{{ $file->withVersion($this->rotatedFiles)->getUrl() }}"
                                     class="card-img"
                                />
                            </x-move-modal>
                        @endif
                        </div>

                        <div class="ml-3 bg-gray-100 p-2 text-sm">

                            @if ($file->guessExtension() !== 'pdf')
                            <div wire:click="rotateFile('{{ $field->attribute }}', {{ $i }})" class="cursor-pointer">
                                <!-- feathericon-rotate-cw -->
                                <svg class="text-gray-400 hover:text-gray-600 h-6 w-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                            </div>
                            @endif

                            <div wire:click="removeFile('{{ $field->attribute }}', {{ $i }})" class="cursor-pointer mt-2">
                                <!-- heroicon-o-trash -->
                                <svg class="text-gray-400 hover:text-gray-600 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>

                            <div class="w-full text-sm" wire:target="rotateFile" wire:loading>

                                <div class="flex">
                                    <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>

                                    <div class="mr-auto text-small" wire:target="removeFile" wire:loading>
                                        @lang('Loading...')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
        </div>
    </x-slot>

</x-move-form.row>
