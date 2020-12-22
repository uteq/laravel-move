@props(['filter'])

<select wire:model="filter.{{ \Str::slug(get_class($filter)) }}" class="block w-full form-control-sm form-select rounded-md border-gray-300">
    <option value="">-</option>
    @foreach ($filter->options() as $label => $value)
        <option value="{{ $value }}" wire:key="{{ $loop->index }}">{{ $label }}</option>
    @endforeach
</select>
