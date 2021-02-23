@props(['value' => null, 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium leading-5 text-gray-700 sm:mt-px sm:pt-2']) }}>
    {!! html_entity_decode($value ?? $slot) !!}
    @if ($required)
        <span class="text-red-500 text-sm">*</span>
    @endif
</label>
