@props(['model', 'options' => [], 'placeholder' => null, 'settings' => [], 'values' => null])

@php $index = \Str::slug(str_replace('.', '-', $model)); @endphp

<div wire:ignore class="w-full">
    <select name="{{ $model }}"
            id="{{ $model }}"
            {{ $attributes->merge(['class' => 'select2-'. $index .' form-select w-full']) }}
    >
        @if (($settings['multiple'] ?? false) !== true)<option></option>@endif
        @forelse($options as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
        @empty
            {{ $slot }}
        @endforelse
    </select>

    <script>
        let loadSelect2{{str_replace("-","_", $index)}} = function() {

            let $element = window.$('.select2-{{ $index }}');
            let val = @php echo json_encode($values && count($values) ? $values : null) @endphp;
            let settings = Object.assign({}, {
                placeholder: '{{ $placeholder ?: __('Select your option')}}',
                allowClear: true,
            }, {!! json_encode($settings, JSON_FORCE_OBJECT) !!});

            // For value to work somehow it needs to initiated twice.
            //  Not very well for performance, but for now it'll have to do
            if (val) {
                $element.select2(settings).val(val);
                $element.select2(settings).val(val);
            } else {
                $element.select2(settings);
            }

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

</div>
