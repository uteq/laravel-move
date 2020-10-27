@props(['submit', 'title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="mt-5 md:mt-0 md:col-span-2">

        <form wire:submit.prevent="{{ $submit }}">

            <div class="shadow overflow-hidden sm:rounded-md">

                <div class="px-4 py-5 bg-white sm:p-6">

                    @if ($title || $description)
                    <div class="border-b pb-3 mb-3">
                        <x-move-section-title>
                            @if ($title) <x-slot name="title">{{ $title }}</x-slot> @endif
                            @if ($description) <x-slot name="description">{{ $description }}</x-slot> @endif
                        </x-move-section-title>
                    </div>
                    @endif

                    <div class="grid grid-cols-6 gap-6">
                        {{ $form }}
                    </div>
                </div>

                @if (isset($actions))
                    <div class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6">
                        {{ $actions }}
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
