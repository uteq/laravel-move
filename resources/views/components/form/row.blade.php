@props(['model', 'required' => false, 'label' => null, 'type' => 'input', 'helpText' => null, 'custom' => false, 'width' => null, 'flex' => true, 'stacked' => false, 'meta' => []])

@php $labelValue = $label @endphp

@if ($stacked)

    @include('move::components.form.row.stacked')

@else

    @include('move::components.form.row.normal')

@endif
