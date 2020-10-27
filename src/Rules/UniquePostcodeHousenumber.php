<?php

namespace Uteq\Move\Rules;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UniquePostcodeHousenumber implements \Illuminate\Contracts\Validation\Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return ! $this->query()->exists();
    }

    public function query(): Builder
    {
        return DB::table(request()->resource)
            ->where('house_number', request()->input('house_number'))
            ->where('postcode', request()->input('postcode'))
            ->where('id', '<>', request()->resourceId);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Deze combinatie postcode huisnummer is al in gebruik onder de naam `' . $this->query()->first()->name .'`.';
    }
}
