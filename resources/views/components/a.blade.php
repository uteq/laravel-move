@props(['button' => false, 'color' => 'primary', 'display' => 'inline-flex', 'margin' => true, 'padding' => true])

<a {{ $attributes->merge([
        'class' => $button
            ? $display . ' items-center '. ($padding ? 'px-4' : null) .' py-2 bg-'. $color .'-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-'. $color .'-700 active:bg-'. $color .'-900 focus:outline-none focus:border-'. $color .'-900 focus:shadow-outline-'. $color .' disabled:opacity-25 transition ease-in-out duration-150'
            : 'cursor-pointer text-'. $color .'-500 ml-auto ' . ($margin ? 'mr-6' : '') . ' hover:text-'. $color .'-800 hover:underline '. ($padding ? 'px-4' : null) .' py'
    ]) }}
>{{ $slot }}</a>
