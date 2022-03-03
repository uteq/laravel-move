@props([
    'title',
    'content' => null,
    'id' => null,
    'maxWidth' => null,
    'button' => null,
    'show' => true,
    'withoutFooter' => false,
    'withoutPadding' => false,
    'close' => false,
    'closeable' => false,
    'flexFooter' => true,
    'withCancelButton' => true,
    'keepConfirmButton' => false,
    'cancelText' => __('Annuleren'),
    'confirmText' => __('OK'),
    'closeOnClickAway' => true,
    'closeBehavior' => 'alpine',
])

<x-move-modal
    :id="$id"
    :maxWidth="$maxWidth"
    :show="$show"
    :button="$button"
    :closeOnClickAway="$closeOnClickAway"
    :closeBehavior="$closeBehavior"
    {{ $attributes }}
>
    <div class="px-6 py-4 bg-white text-lg sticky w-full top-0 shadow z-10">
        @if ($close || $closeable)
            <div class="flex items-center justify-between bg-white">
                {!! $title !!}

                <span
                    <?php if ($closeBehavior === 'alpine'): ?> x-on:click="show = null" <?php endif; ?>
                    <?php if ($closeBehavior === 'livewire'): ?> wire:click="closeModal" <?php endif; ?>
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
            </div>
        @else
            {!! $title !!}
        @endif
    </div>

    <div class="text-left break-words whitespace-wrap overflow-y-auto" style="max-height: calc(100vh - 220px)">
        <div class="{{ $withoutPadding ?: 'p-6' }}">
            {!! $content ?? $slot !!}
        </div>
    </div>

    @if (! $withoutFooter)
    <div class="px-6 py-4 bg-gray-100 text-right sticky w-full bottom-0">
        @if ($flexFooter)
            <div class="flex items-center justify-between">
        @endif

        @if ($withCancelButton)
            @if ($closeBehavior === 'alpine')
                <x-move-secondary-button x-on:click="show = null">{{ $cancelText }}</x-move-secondary-button>
            @elseif ($closeBehavior === 'livewire')
                <x-move-secondary-button wire:click="closeModal">{{ $cancelText }}</x-move-secondary-button>
            @endif
        @endif

        @if ($footer ?? null)
            {{ $footer }}
        @endif

        @if((! ($footer ?? null)) || $keepConfirmButton)
            <x-move-button type="button" x-on:click="show = null">
                {{ $confirmText }}
            </x-move-button>
        @endif

        @if ($flexFooter)
            </div>
        @endif
    </div>
    @endif
</x-move-modal>
