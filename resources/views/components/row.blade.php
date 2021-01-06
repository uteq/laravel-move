<div {{ $attributes->merge(['class' => 'flex border-b border-gray-100 border-40']) }}>
    <div class="w-1/4 py-4 {{ $titleClass ?? '' }}">
        <h4 class="font-normal text-gray-500">{!! $name !!} </h4>
    </div>
    <div class="w-3/4 py-4 break-words {{ $slotClass ?? '' }}">
        <p class="text-gray-700">
            {!! $slot !!}
        </p>
    </div>
</div>
