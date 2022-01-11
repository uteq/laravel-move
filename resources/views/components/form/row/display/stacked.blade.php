<div class="{{ $meta['stacked_classes'] ?? 'md:px-4 lg:px-6 pt-0 pb-2 last:pb-4 bg-white w-full border-b last:border-b-0' }}">
    @if ($labelValue)
        <x-move-form.label
            :help-text="$helpText && ($meta['help_text_location'] ?? 'below') == 'hidden' ? $helpText : null"
            :required="$required"
            class="mb-2"
            for="{{ $model }}"
            value="{{ $labelValue }}"
        ></x-move-form.label>
    @endif

    @if ($helpText && in_array($meta['help_text_location'] ?? 'below', ['above', 'top']))
        <div class="mb-2 border-l pl-2 text-xs">
            {!! $helpText !!}
        </div>
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

    @if (session()->has($model .'.message') || $this->message)
        <div class="absolute top-0 right-0 bg-green-500 text-white py-2 text-center text-sm px-4 font-bold"
             x-data="{ show: true }"
             x-show="show"
             x-init="setTimeout(() => show = false, 3000)"
             wire:key="{{ $model . '.message' }}{{ \Str::random() }}"
        >
            {!! session($model .'.message', $this->message) !!}
        </div>
    @endif

    <x-move-form.input-error for="{{ $model }}" class="mt-2" />

        @if ($helpText && in_array($meta['help_text_location'] ?? 'below', ['below', 'bottom']))
        <div class="mb-2 border-l pl-2 text-xs">
            @include('move::components.form.row.helptext', [
                'textSize' => 'text-xs',
            ])
        </div>
    @endif
</div>
