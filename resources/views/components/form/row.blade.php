@props(['model', 'required' => false, 'label', 'type' => 'input', 'helpText' => null, 'custom' => false, 'width' => null, 'flex' => true, 'stacked' => false, 'meta' => []])

@php $labelValue = $label @endphp

@if ($stacked)

    <div class="md:px-4 pt-0 pb-2 last:pb-4 bg-white w-full border-b last:border-b-0 border-gray-100">
        @if ($labelValue)
        <x-move-form.label for="{{ $model }}"
                           value="{{ $labelValue }}"
                           :required="$required"
        />
        @endif

        @if (!$custom)
            <x-dynamic-component
                component="move::field.{{ $type }}"
                model="{{ $model }}"
                {{ $attributes }}
                :required="$required"
            />
        @else
            {{ $slot }}
        @endif
    </div>

@else
<div class="px-4 pt-4 pb-2 last:pb-4 bg-white sm:p-6 w-full border-b {{ ($meta['with_grid'] ?? true) ? 'grid grid-cols-6 gap-6' : null }} last:border-b-0 border-gray-100" wire:key="move-form-row-{{ $model }}">
    <div class="col-span-6 sm:col-span-4">
        <div class="{{ $labelValue ? 'sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start' : null }}">
            @if ($labelValue)
            <x-move-form.label for="{{ $model }}"
                          value="{{ $labelValue }}"
                          :required="$required"
            />
            @endif

            <div class="mt-1 sm:mt-0 {{ $labelValue ? 'sm:col-span-2' : null }}">
                <div class="{{$width}} @if ($flex) flex @endif @if (!$custom) @if ($type === 'checkbox') mt-2 @else rounded-md shadow-sm @endif @endif">
                    @if (!$custom)
                    <x-dynamic-component
                        component="move::field.{{ $type }}"
                        model="{{ $model }}"
                        {{ $attributes }}
                        :required="$required"
                    />
                    @else
                        {{ $slot }}
                    @endif
                </div>

                @if ($helpText)
                    <div class="mt-2 help-text text-gray-500 text-sm">
                        {!! $helpText !!}
                    </div>
                @endif

                <x-move-form.input-error for="{{ $model }}" class="mt-2"/>

                @if ($append ?? null)
                    {!! $append !!}
                @endif
            </div>

        </div>
    </div>
</div>
@endif
