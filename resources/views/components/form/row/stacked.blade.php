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


    <x-move-form.input-error for="{{ $model }}" class="mt-2"/>
</div>
