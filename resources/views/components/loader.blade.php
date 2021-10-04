@props(['target' => null, 'message' => null])

<div {{ $attributes->merge(['class' => 'absolute w-full h-full flex justify-center items-center z-40 bg-white']) }}
    <?php if ($target): ?>
     wire:loading.flex
     wire:target="{{ $target }}"
    <?php endif; ?>
>
    <div class="text-center flex flex-col justify-center items-center gap-2">
        <x-move-loader-icon />

        {{ $message ?: 'Aan het laden...' }}
    </div>
</div>
