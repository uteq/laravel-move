@props(['filter'])

<label>
    <input type="radio" wire:model="filter.{{ \Str::slug(get_class($filter)) }}" value="" /> Allen
</label>
@foreach ($filter->options() as $label => $value)
<label>
    <input type="radio" wire:model="filter.{{ \Str::slug(get_class($filter)) }}" wire:key="{{ $loop->index }}" value="{{ $value }}" />
    {{ $label }}
</label>
@endforeach
