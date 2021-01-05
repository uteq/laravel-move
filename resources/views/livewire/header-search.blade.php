<form x-data="{ open: @entangle('showSearchResult') }"
      @click.away="open = false"
      class="w-full flex md:ml-0"
      action="#"
      method="GET"
>
    <label for="search_field" class="sr-only">{{ __('Press / to search') }}</label>
    <div class="relative w-full text-gray-400 focus-within:text-gray-600">
        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" />
            </svg>
        </div>
        <input x-on:click="open = true" wire:model="search" id="search_field" class="border-none block w-full h-full pl-8 pr-3 py-2 rounded-md text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 sm:text-sm" placeholder="Press / to search" type="search">
    </div>
    <div x-show="open" style="display: none; width: 450px;" class="absolute top-16 left-14 max-w-xl z-10">
        <div class="p-3 border w-full rounded-bottom bg-white " wire:target="search" wire:loading>
            Aan het laden...
        </div>

        @if (strlen($search))
            <div id="search-dropdown"
                 class="border w-full rounded-bottom bg-white"
                 wire:target="search"
                 wire:loading.remove
                 style="overflow: auto; max-height: calc(100vh - 64px);"
            >
                @forelse ($searchResult as $result)
                    <div class="text-gray-600 px-5 pb-2 pt-3">
                        {{ $result['resource']::label() }}
                    </div>

                    <ul>
                        @foreach ($result['result'] as $model)
                            @if (!is_object($model))
                                @continue
                            @endif
                            <li class="w-full focus:bg-{{ move()::getThemeColor() }}-100 hover:bg-{{ move()::getThemeColor() }}-50">
                                <a href="{{ route(move()::getPrefix() . '.show', ['resource' => $result['route'], 'model' => $model]) }}" class="cursor-pointer w-full block py-1 px-5">
                                    {{ $result['resource']::title($model) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @empty
                    <div class="p-3">
                        Geen resultaten gevonden
                    </div>
                @endforelse
                @endif
            </div>
</form>

<script defer>
    document.addEventListener("livewire:load", function() {
        $('#search-dropdown ul').first().focus();
    });

    document.addEventListener("keyup", event => {

        if (event.key === '/') {
            if (["INPUT", "TEXTAREA"].includes(document.activeElement.tagName)) {
                return;
            }

            window.livewire.emit('startSearch')
            document.getElementById('search_field').focus();
        }

        if (event.key === 'Escape') {
            window.livewire.emit('stopSearch');
            document.getElementById('search_field').blur();
        }
    });
</script>
