<?php

namespace Uteq\Move\Fields;

class Row extends Panel
{
    public string $component = 'form.row';

    public string $containerClass;

    public function __construct(array $fields)
    {
        parent::__construct(null, $fields);
    }

    public function init()
    {
        $this->containerClass = 'flex gap-4 mb-4 bg-white w-full';

        $this->stackFields();
    }

    public function isInline(): static
    {
        $this->containerClass = 'flex gap-4 bg-white w-full mb-0.5';

        return $this;
    }

    public function title($title)
    {
        $this->meta['title'] = $title;

        return $this;
    }
}
