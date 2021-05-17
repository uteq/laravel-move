@props(['button' => false, 'color' => 'primary'])

<a {{ $attributes->merge([
        'class' => $button
            ? 'inline-flex items-center px-4 py-2 bg-'. $color .'-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-'. $color .'-700 active:bg-'. $color .'-900 focus:outline-none focus:border-'. $color .'-900 focus:shadow-outline-'. $color .' disabled:opacity-25 transition ease-in-out duration-150'
            : 'cursor-pointer text-'. $color .'-500 mr-6 hover:text-'. $color .'-800 hover:underline px-4 py'
    ]) }}
>{{ $slot }}</a>
