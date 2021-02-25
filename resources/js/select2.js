require('select2');

export default function(element, val, settings, options, onChangeCallback) {

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
};
