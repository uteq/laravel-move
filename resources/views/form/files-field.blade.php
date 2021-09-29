<x-move-form.row width="w-full" custom label="{{ $field->name }}" model="{{ $field->store }}" :required="$field->isRequired()" help-text="{{ $field->getHelpText() }}">

    <label class="w-full">
        <div class="flex text-center items-center px-4 py-2 bg-white text-blue rounded-lg tracking-wide uppercase border border-blue cursor-pointer hover:bg-teal-800 hover:text-white hover:bg-primary-500">
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
               accept="{{ $field->getAccept() }}"
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
                        <div class="flex w-full" <?php if ($file->guessExtension() === 'pdf') : ?> style="height: 50vh" <?php endif; ?>>

                            @if ($file->guessExtension() === 'pdf')

                                <div class="card-body flex-grow flex flex-col">
                                    <a href="{{ $file->getUrl() }}"
                                       target="_blank"
                                       class="flex flex-0 w-full justify-center items-center text-center"
                                    >
                                        {{ $file->getClientOriginalName() }}
                                        <!-- heroicon-o-external-link -->
                                        <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>

                                    <object class="flex-grow" data="{{ $file->getUrl() }}" type="application/pdf" width="100%" height="100%">
                                        <a href="{{ $file->getUrl() }}">{{ $file->getClientOriginalName() }}</a>
                                    </object>
                                </div>

                            @else
                                <div class="flex-grow">
                                    <x-move-modal wire:model="showFile.{{ $i }}">
                                        <x-slot name="button">
                                            <div class="cursor-pointer" data-lightbox="roadtrip">
                                                <div class="ml-4 text-sm text-center py-2">{{ $file->getClientOriginalName() }}</div>
                                                <img src="{{ $file->withVersion($this->rotatedFiles)->getUrl() }}"
                                                     class="object-contain h-48 w-full"
                                                />
                                            </div>
                                        </x-slot>

                                        <img src="{{ $file->withVersion($this->rotatedFiles)->getUrl() }}"
                                             class="card-img"
                                        />
                                    </x-move-modal>
                                </div>
                            @endif

                            <div class="{{ $file->guessExtension() === 'pdf' ?: 'ml-3' }} bg-gray-100 p-2 text-sm flex flex-col gap-2">

                                @if ($file->guessExtension() !== 'pdf' && $field->showRotate)
                                    <div wire:click="rotateFile('{{ $field->attribute }}', {{ $i }})" class="cursor-pointer">
                                        <!-- feathericon-rotate-cw -->
                                        <svg class="text-gray-400 hover:text-gray-600 h-6 w-6" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                                    </div>
                                @endif

                                <div wire:click="removeFile('{{ $field->attribute }}', {{ $i }})" class="cursor-pointer">
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
