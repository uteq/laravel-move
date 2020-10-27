@php $this->loadFiles($field) @endphp

@if ($this->files)
    @foreach ($this->loadFiles($field) as $i => $file)

        @if (!$file instanceof \Uteq\Move\File\ResourceFileContract)
            @continue
        @endif

        @if (!$file->exists())
            @continue
        @endif

        <div class="w-full md:w-1/2 @if ($i % 1 === 0) pr-6 @endif ">
            <div wire:key="file{{ $field->attribute }}{{ $loop->index }}{{ $i }}" class="border p-3 rounded my-3">

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
                        <div wire:click="showFile({{ $i }})" class="cursor-pointer" data-lightbox="roadtrip">
                            <img src="{{ $file->getUrl() }}"
                                 class="card-img"
                            />
                        </div>

                        <x-move-modal id="showingFile.{{ $i }}" wire:model="showingFile.{{ $i }}">
                            <img src="{{ $file->getUrl() }}"
                                 class="card-img"
                            />
                        </x-move-modal>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
@endif
