<div class="{{ $meta['classes'] ?? '' }}">

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

    @include('move::components.form.model-message')

    <x-move-form.input-error for="{{ $model }}" class="mt-2"/>

</div>
