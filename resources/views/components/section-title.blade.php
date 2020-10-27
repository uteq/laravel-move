<div class="md:col-span-1">
    <div class="px-4 sm:px-0">
        @if ($title ?? null)
        <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
        @endif

        @if ($description ?? null)
        <p class="mt-1 text-sm text-gray-600">
            {{ $description }}
        </p>
        @endif
    </div>
</div>
