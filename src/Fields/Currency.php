<?php

namespace Uteq\Move\Fields;

use Illuminate\Support\Str;
use Stringable;

class Currency extends Number
{
    public array $rules = [];

    public function numberFormat(int $decimals = 0, string $decimalSeparator = '.', string $thousandSeparator = ','): static
    {
        $this->resourceDataCallback = function ($value): string {
            return Str::of($value)
                ->rtrim('0')
                ->when(str_ends_with($value, '.'), function($string) {
                   return $string->replace('.', '');
                });
        };

        $this->beforeStore(fn () => $this->storeFormat(fn () => $decimals));

        return $this;
    }
}
