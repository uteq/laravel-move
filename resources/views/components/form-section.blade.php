@props(['submit'])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="mt-5 md:mt-0 md:col-span-2">

        <form wire:submit.prevent="{{ $submit }}">
            {{ $form }}

            @if (isset($actions))
                <div class="shadow flex items-center justify-end px-4 py-3 bg-white text-right sm:px-6 rounded border border-gray-100 mt-5">
                    {{ $actions }}
                </div>
            @endif
        </form>
    </div>
</div>
