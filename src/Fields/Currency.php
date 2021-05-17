<?php

namespace Uteq\Move\Fields;

class Currency extends Number
{
    public array $rules = [];

    public function numberFormat(int $decimals = 0, string $decimalSeparator = '.', string $thousandSeparator = ','): self
    {
        $this->resourceDataCallback = function ($value) {

            $value = rtrim($value, '0');
            $value = (substr($value, -1) === '.') ? str_replace('.', '', $value) : $value;

            return $value;
        };

        $this->beforeStore(fn ($value) => ($this->storeFormat)($value, $decimals));

        return $this;
    }
}
