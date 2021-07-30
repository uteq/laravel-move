<?php

namespace Uteq\Move\Fields;

class YesNo extends RadioGroup
{
    public function init()
    {
        $this->options([
            'yes' => __('Yes'),
            'no' => __('No'),
        ]);
    }

    public function applyResourceData(
        $resource,
        $attribute = null
    ): self
    {
        parent::applyResourceData($resource, $attribute,);

        if (is_bool($this->value) || is_int($this->value)) {
            $this->value = $this->value == 1 ? 'yes' : 'no';
        }

        return $this;
    }
}
