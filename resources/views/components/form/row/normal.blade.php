<div class="px-4 pt-4 pb-2 last:pb-4 bg-white sm:p-6 w-full border-b {{ ($meta['with_grid'] ?? true) ? 'grid grid-cols-6 gap-6' : null }} last:border-b-0 border-gray-100" wire:key="move-form-row-{{ $model }}">

    @if ($meta['full_colspan'] ?? false === true)

        <div class="{{ ($labelValue ? 'sm:grid sm:grid-cols-9 sm:gap-4 sm:items-start' : null) }}">
            @if ($labelValue)
                <div class="col-span-2">
                    @include('move::components.form.row.label')

                    @include('move::components.form.row.helptext-hidden')
                </div>
            @endif

            <div class="mt-1 sm:mt-0 col-span-7">
                @include('move::components.form.row.content')

                @include('move::components.form.row.helptext')

                <x-move-form.input-error for="{{ $model }}" class="mt-2"/>

                @if ($append ?? null)
                    {!! $append !!}
                @endif
            </div>
        </div>

    @else

        <div class="col-span-6 sm:col-span-4">
            <div class="{{ ($labelValue ? 'sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start' : null) }}">
                @if ($labelValue)
                    @include('move::components.form.row.label')
                @endif

                <div class="mt-1 sm:mt-0 {{ $labelValue ? 'sm:col-span-2' : null }}">
                    @include('move::components.form.row.content')

                    @include('move::components.form.row.helptext')

                    <x-move-form.input-error for="{{ $model }}" class="mt-2"/>

                    @if ($append ?? null)
                        {!! $append !!}
                    @endif
                </div>
            </div>
        </div>

        @if (($meta['help_text_location'] ?? 'below') == 'after_hidden')
            @include('move::components.form.row.helptext-hidden')
        @endif

    @endif
</div>
