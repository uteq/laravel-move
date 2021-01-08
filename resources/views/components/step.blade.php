@props(['title' => null, 'description' => null, 'panel' => null, 'hideTitle' => false])

@if ($title || $description)
    @if ($title && !$hideTitle) <h1 class="text-4xl font-semibold text-gray-900 mt-5">{{ $title }}</h1> @endif
    @if ($description) <p>{{ $description }}</p> @endif
@endif

<div {{ $attributes->merge(['class' => 'overflow-hidden']) }}>

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
</div>
