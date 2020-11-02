@props(['title' => null, 'description' => null])

@if ($title || $description)
    @if ($title) <h2 class="text-2xl font-semibold text-gray-900 mt-5">{{ $title }}</h2> @endif
    @if ($description) <p>{{ $description }}</p> @endif
@endif

<div {{ $attributes->merge(['class' => 'shadow overflow-hidden sm:rounded-md']) }}>

    <div class="bg-white">
        {{ $slot }}
    </div>

</div>
