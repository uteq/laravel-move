<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Support\Str;

class Number extends Field
{
    public string $component = 'number-field';

    public string $step = '1';

    public int $decimals = 0;

    protected Closure $displayFormat;
    protected Closure $storeFormat;

    public function init(): void
    {
        $this->numberFormat($this->decimals, ',', '');

        $this->displayFormat = function ($value, $decimals, $decimalSeparator, $thousandSeparator): ?string {
            return $value
                ? number_format((float)$value, $decimals, $decimalSeparator, $thousandSeparator)
                : null;
        };

        $this->storeFormat = function ($value, $decimals): ?string {
            return $value
                ? number_format(
                    (float)str_replace(',', '.', $value),
                    $decimals,
                    '.',
                    ''
                )
                : null;
        };
    }

    public function decimals(int $decimals): static
    {
        $this->step = $decimals
            ? '0.'. Str::padLeft('1', $decimals, '0')
            : '1';

        $this->decimals = $decimals;

        $this->numberFormat($this->decimals);

        return $this;
    }

    public function step($step): static
    {
        $this->step = $step;

        return $this;
    }

    public function numberFormat(
        int $decimals = 0,
        string $decimalSeparator = '.',
        string $thousandSeparator = ','
    ): static {
        $this->resourceDataCallback = fn ($value) => ($this->displayFormat)(
            $value,
            $decimals,
            $decimalSeparator,
            $thousandSeparator
        );

        $this->beforeStore(fn ($value) => ($this->storeFormat)($value, $decimals));

        return $this;
    }

    public function displayFormat(Closure $displayFormat): static
    {
        $this->displayFormat = $displayFormat;

        return $this;
    }

    public function storeFormat(Closure $storeFormat): static
    {
        $this->storeFormat = $storeFormat;

        return $this;
    }
}
