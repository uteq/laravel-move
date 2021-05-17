<?php

namespace Uteq\Move\Fields;

use Illuminate\Support\Str;

class Number extends Field
{
    public string $component = 'number-field';

    public string $step = '1';

    public int $decimals = 0;

    public \Closure $displayFormat;
    public \Closure $storeFormat;

    public function init()
    {
        $this->numberFormat($this->decimals, ',', '');
        $this->initDefaultDisplayFormat();
        $this->initDefaultStoreFormat();
    }

    public function initDefaultDisplayFormat()
    {
        $this->displayFormat = function ($value, $decimals, $decimalSeparator, $thousandSeparator) {
            return $value
                ? number_format((float)$value, $decimals, $decimalSeparator, $thousandSeparator)
                : null;
        };
    }

    public function initDefaultStoreFormat()
    {
        $this->storeFormat = function ($value, $decimals) {
            return $value
                ? number_format((float)str_replace(',', '.', $value), $decimals, '.', '')
                : null;
        };
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
        $this->resourceDataCallback = fn ($value) => ($this->displayFormat)($value, $decimals, $decimalSeparator, $thousandSeparator);

        $this->beforeStore(fn ($value) => ($this->storeFormat)($value, $decimals));

        return $this;
    }

    public function displayFormat(\Closure $displayFormat)
    {
        $this->displayFormat = $displayFormat;

        return $this;
    }

    public function storeFormat(\Closure $storeFormat)
    {
        $this->storeFormat = $storeFormat;

        return $this;
    }
}
