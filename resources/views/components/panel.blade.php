@props(['title' => null, 'description' => null, 'panel' => null, 'afterTitle' => null])

@if ($title || $description || optional($panel)->description)
    <div class="flex items-center justify-between">
        <div>
        @if ($title) <h2 class="{{ ($panel->class ?? null) ?: 'text-2xl font-semibold text-gray-900 mt-5 mb-2' }}">{{ $title }}</h2> @endif
        @if ($description || ($panel && $panel->description))
            <p class="mb-5 text-lg">{{ $description ?: $panel->description ?? null }}</p>
        @endif
        </div>

        @if ($afterTitle)
            {!! $afterTitle($panel) !!}
        @endif
    </div>
@endif

@if (! (optional($panel)->withoutCard ?? false))
<div {{ $attributes->merge(['class' => 'shadow overflow-hidden sm:rounded-md']) }}>

    <div class="bg-white">
@endif

        @if ($panel && count($panel->alert))
            <div class="p-6">
                @foreach ($panel->alert as $type => $alerts)
                    @foreach ($alerts as $alert)
                        <div class="bg-blue-50 border-t-4 border-blue-400 rounded-b text-blue-900 px-4 py-3 shadow-md" role="alert">
                            <p class="text-sm">{!! $alert !!}</p>
                        </div>
                    @endforeach
                 @endforeach
            </div>
        @endif

        {{ $slot }}

@if (! (optional($panel)->withoutCard ?? false))
    </div>
</div>
@endif
