@props(['title', 'add-url', 'add-action', 'add-text', 'search' => false])

<div class="-ml-4 -mt-2 flex items-end justify-between flex-wrap sm:flex-no-wrap">
    <div class="ml-4 mt-2 {{ $search ? '' : 'mb-12' }}">
        @if ($search)
        <div class="relative mt-3 w-full text-gray-400 focus-within:text-gray-600">
            <div class="absolute inset-y-0 left-2 flex items-center pointer-events-none">
                <div wire:loading wire:target="search">
                    <x-css-spinner class="h-5 w-5 animate-spin" />
                </div>
                <div wire:loading.remove wire:target="search">
                    <x-heroicon-o-search class="h-5 w-5" />
                </div>
            </div>
            <input id="search_field"
                   class="shadow block w-full h-full pl-10 pr-3 py-2 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 text-sm"
                   placeholder="Zoeken"
                   type="search"
                   wire:model="filter.search"
            />
        </div>
        @endif
    </div>
    <div class="ml-4 sm:ml-0">
        <x-move-button wire:click="{{ $addAction }}" class="font-black">{{ $addText }}</x-move-button>
    </div>
</div>
