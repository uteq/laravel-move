@props([
    'model',
    'options' => [],
    'placeholder' => null,
    'settings' => ['placeholder' => __('Select your option')],
    'values' => null,
    'multiple' => false,
    'tags' => false,
])

@php $index = \Str::slug(str_replace('.', '-', $model)); @endphp

<div class="w-full" wire:ignore>
    <select
        name="{{ $model }}"
        id="{{ $model }}"
        {{ $attributes->merge(['class' => 'select2-'. $index .' form-select rounded border-none shadow w-full']) }}
        @if ($multiple) multiple="multiple" @endif
        @if ($tags) tags="tags" @endif
        style="width: 100%"
    >
        @forelse($options as $key => $label)
            <option
                value="{{ $key }}"
                @if ($values && is_array($values))
                    @if (isset(array_flip($values)[$label])) selected="selected" @endif
                    @if (isset(array_flip($values)[$key])) selected="selected" @endif
                @elseif((string)$key === (string)$values)
                    selected="selected"
                @endif
            >{{ $label }}</option>
        @empty
            {{ $slot }}
        @endforelse
    </select>
</div>

<script>

    if (typeof window.loadSelect2 === 'undefined') {

        window.loadSelect2 = function(element, val, settings, options, onChangeCallback) {

            function parse(obj) {
                for (const index in obj) {
                    let value = obj[index];

                    if (typeof value === 'string' && value.startsWith('function')) {
                        eval('value = ' + value);
                    }

                    if (typeof value === 'object') {
                        value = parse(value);
                    }

                    obj[index] = value;
                }

                return obj;
            }

            let $element = window.$(element);

            settings = parse(Object.assign({
                placeholder: "",
                allowClear: true,
            }, settings));

            options = Object.assign({
                isMultiple: false,
            }, options);

            if (val && ! options['isMultiple']) {
                $element.select2(settings).val(val).trigger('change');
            } else {
                $element.select2(settings);
            }

            $element.on('change', onChangeCallback);
            $element.on('open', function() {
                self.$search.attr('tabindex', 0);
                setTimeout(function () { self.$search.focus(); }, 10);
            })
        };
    }

    let select2Loader{{str_replace("-","_", $index)}} = () => {
        let element = '.select2-{{ $index }}';
        let val = @php echo json_encode($values && count($values) ? $values : null) @endphp ?? {};
        let settings = {!! json_encode(array_replace([
            'placeholder' => $placeholder,
            'tags' => $tags,
        ], $settings)) !!} ?? {};

        loadSelect2(element, val, settings, {
            isMultiple: @php echo json_encode($multiple ?? false) @endphp,
        }, function (e) {

            var data = $(this).val();
            let elementName = $(this).attr('id');

            @this.set(elementName, data);
        });
    };

    document.addEventListener("livewire:load", () => {
        select2Loader{{str_replace("-","_", $index)}}();

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
    });

    if (typeof window.$ !== 'undefined') {
        select2Loader{{str_replace("-","_", $index)}}();
    }

</script>
