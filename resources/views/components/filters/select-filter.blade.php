@props(['filter'])

<div class="w-full">
    <x-move-field.select
        model="filter.{{ \Str::slug(get_class($filter)) }}"
    >
        <option value="">-</option>
        @foreach ($filter->options() as $label => $value)
            <option value="{{ $value }}" wire:key="{{ $loop->index }}">{{ $label }}</option>
        @endforeach
    </x-move-field.select>
</div>
