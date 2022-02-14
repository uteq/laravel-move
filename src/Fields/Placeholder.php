<?php

namespace Uteq\Move\Fields;

class Placeholder extends Panel
{
    public function __construct(array $fields = [])
    {
        return parent::__construct(uniqid(), $fields);
    }
}
