@props(['sortable' => false, 'sort' => 'both'])

<th {{ $attributes->merge([
    'class' => 'px-3 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider'
]) }}>
    <span class="{{ $sortable ? 'cursor-pointer inline-flex items-center' : '' }}">
        {{ $slot }}
        @if ($sortable)
            @if ($sort === 'both')
                <x-fas-sort class="h-4 w-4 ml-1 text-gray-400" />
            @elseif ($sort === 'up')
                <x-fas-sort-up class="h-4 w-4 ml-1 text-gray-400" />
            @elseif ($sort === 'down')
                <x-fas-sort-down class="h-4 w-4 ml-1 text-gray-400" />
            @endif
        @endif
    </span>
</th>

