@props(['value' => null, 'required' => false, 'helpText' => null])

<div class="text-gray-700" x-data="{ open : false }">
    <div class="flex items-center sm:mt-px sm:pt-2 gap-1.5">
        <label {{ $attributes->merge(['class' => 'block text-sm font-medium leading-5 text-gray-700']) }}>
            {!! html_entity_decode($value ?? $slot) !!}
            @if ($required)
                <span class="text-red-500 text-sm">*</span>
            @endif
        </label>

        @if ($helpText)
            <button type="button" x-on:click="open = ! open">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
        @endif
    </div>

    <div class="text-xs mt-4" x-cloak x-show="open">
        {{ $helpText }}
    </div>
</div>
