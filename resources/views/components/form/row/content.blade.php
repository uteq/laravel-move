<div class="{{$width}} {{ $flex ? 'flex' : null }} @if (!$custom) @if ($type === 'checkbox') mt-2 @else rounded-md shadow-sm @endif @endif">
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
</div>
