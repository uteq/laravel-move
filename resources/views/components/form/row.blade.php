@props(['model', 'required' => false, 'label' => null, 'type' => 'input', 'helpText' => null, 'custom' => false, 'width' => null, 'flex' => true, 'stacked' => false, 'meta' => []])

@php
    $labelValue = $label;
    $display = $meta['display'] ?? 'normal';
@endphp

@if (str_contains($display, '.'))

    @include($display)

@else

    @include('move::components.form.row.display.' . $display)

@endif
