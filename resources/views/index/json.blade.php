<div>
    @if ($field->indexDisplayType === 'modal')

    <x-move-modal>
        <x-slot name="button">
            <span class="text-primary-500 cursor-pointer">
                {{ __('Show') }}
            </span>
        </x-slot>

        <div class="p-4">
            <div class="font-bold text-lg">{{ $field->name }}</div>

            <div class="grid gap-4 grid-cols-2">
                @foreach ($field->value as $key => $value)
                    <div>{{ $key + 1 }}</div>
                    <div>{{ $value }}</div>
                @endforeach
            </div>
        </div>
    </x-move-modal>
    @else
        @foreach ($field->value as $key => $value)
        <div class="flex gap-2">
            <div class="text-gray-400">{{ $key + 1 }}:</div>
            <div>{{ $value }}</div>
        </div>
        @endforeach
    @endif
</div>
