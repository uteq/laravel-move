<?php

namespace Uteq\Move\Fields;

class Column extends Text
{
    public function __construct($name, \Closure $content)
    {
        parent::__construct($name);

        $this->onlyOnIndex()
            ->isPlaceholder();

        $this->index = $content;
    }
}
