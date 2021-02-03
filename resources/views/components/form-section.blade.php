@props(['submit', 'sidebarEnabled' => false, 'actions' => null])

<div {{ $attributes->merge(['class' => '']) }}>
    <div class="mt-5 md:mt-0 md:col-span-2">

        <form wire:submit.prevent="{{ $submit }}">

            {!! $head ?? null !!}

            <div class="lg:flex gap-4">
                <div class="lg:flex-grow">
                    {{ $form }}
                </div>

                @if ($sidebarEnabled)
                    <div class="lg:flex-none lg:w-1/3">
                        {!! $sidebar ?? null !!}

                        @if (isset($actions))
                            <div class="shadow flex items-center px-4 py-3 bg-white sm:px-6 rounded border border-gray-100 mt-5">
                                {{ $actions }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            @if (! $sidebarEnabled)
                @if (isset($actions))
                    <div class="shadow flex items-center px-4 py-3 bg-white sm:px-6 rounded border border-gray-100 mt-5">
                        {{ $actions }}
                    </div>
                @endif
            @endif
        </form>
    </div>
</div>
