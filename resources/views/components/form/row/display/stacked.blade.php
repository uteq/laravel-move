<div class="{{ $meta['stacked_classes'] ?? 'md:px-4 pt-0 pb-2 last:pb-4 bg-white w-full border-b last:border-b-0' }}">
    @if ($labelValue)
        <x-move-form.label
            for="{{ $model }}"
            value="{{ $labelValue }}"
            :required="$required"
            class="mb-2"
            :helpText="$helpText && ($meta['help_text_location'] ?? 'below') == 'hidden' ? $helpText : null"
        />
    @endif

    @if (!$custom)
        <x-dynamic-component
            component="move::field.{{ $type }}"
            model="{{ $model }}"
            {{ $attributes }}
            :required="$required"
            :has-error="$errors->has($model)"
        />
    @else
        {{ $slot }}
    @endif

    @if ($append ?? null)
        {!! $append !!}
    @endif

    @if (session()->has($model .'.message'))
        <div class="absolute top-0 right-0 bg-green-500 text-white py-2 text-center text-sm px-4 font-bold"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
        >
            {{ session($model .'.message') }}
        </div>
    @endif

    <x-move-form.input-error for="{{ $model }}" class="mt-2"/>
</div>
