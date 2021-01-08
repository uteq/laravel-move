<?php


namespace Uteq\Move\Fields;

class Number extends Field
{
    public string $component = 'number-field';

    public function numberFormat(int $decimals = 0, string $decimalSeparator = '.', string $thousandSeparator = ','): self
    {
        $this->displayCallback = function ($value, $resource, $attribute) use ($decimals, $decimalSeparator, $thousandSeparator) {
            return $value
                ? number_format((float)$value, $decimals, $decimalSeparator, $thousandSeparator)
                : null;
        };

        $this->beforeStore(function ($value, $field, $model, $data) use ($decimals) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);

            return $value
                ? number_format((float)$value, $decimals, '.', '')
                : null;
        });

        return $this;
    }
}
