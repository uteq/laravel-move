@props(['model', 'options' => [], 'placeholder' => null])

@php $index = \Str::slug(str_replace('.', '-', $model)); @endphp

<div wire:ignore class="w-full">
    <select name="{{ $model }}"
            id="{{ $model }}"
            {{ $attributes->merge(['class' => 'select2-'. $index .' form-select w-full']) }}
    >
        <option></option>
        @forelse($options as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
        @empty
            {{ $slot }}
        @endforelse
    </select>
</div>

<script>
    let loadSelect2{{str_replace("-","_", $index)}} = function() {

        let $element = window.$('.select2-{{ $index }}');

        $element.select2({
            placeholder: '{{ $placeholder ?: __('Select your option')}}',
            allowClear: true,
        });

        $element.on('change', function (e) {
            let elementName = $(this).attr('id');
            var data = $(this).select2("val");
        @this.set(elementName, data);
        });
    };

    document.addEventListener("livewire:load", loadSelect2{{str_replace("-","_", $index)}});

    if (typeof window.$ !== 'undefined') {
        loadSelect2{{str_replace("-","_", $index)}}();
    }
</script>
