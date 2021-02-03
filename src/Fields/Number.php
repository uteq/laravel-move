<?php

namespace Uteq\Move\Fields;

use Illuminate\Support\Str;

class Number extends Field
{
    public string $component = 'number-field';

    public string $step = '1';

    public int $decimals = 0;

    public function init()
    {
        $this->numberFormat($this->decimals, ',', '');
    }

    public function decimals(int $decimals)
    {
        $this->step = $decimals ? '0.'. Str::padLeft('1', $decimals, '0') : '1';
        $this->decimals = $decimals;

        $this->numberFormat($this->decimals);

        return $this;
    }

    public function step($step)
    {
        $this->step = $step;

        return $this;
    }

    public function numberFormat(int $decimals = 0, string $decimalSeparator = '.', string $thousandSeparator = ','): self
    {
        $this->resourceDataCallback = $this->displayFormat($decimals, $decimalSeparator, $thousandSeparator);

        $this->beforeStore($this->storeFormat($decimals));

        return $this;
    }

    protected function displayFormat($decimals, $decimalSeparator, $thousandSeparator)
    {
        return function ($value, $resource, $attribute) use ($decimals, $decimalSeparator, $thousandSeparator) {
            return $value
                ? number_format((float)$value, $decimals, $decimalSeparator, $thousandSeparator)
                : null;
        };
    }

    protected function storeFormat($decimals)
    {
        return function ($value, $field, $model, $data) use ($decimals) {
            $value = str_replace(',', '.', $value);

            return $value
                ? number_format((float)$value, $decimals, '.', '')
                : null;
        };
    }
}
