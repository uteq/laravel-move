<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Facades\Move;

class Show extends Panel
{
    public string $component = 'form.show';

    public string $showResource;

    public bool $hideActions = false;

    public function __construct($name, $fields = [])
    {
        parent::__construct($name, count($fields)
            ? $fields
            : [Text::make('placeholder')->isPlaceholder()],
        );
    }

    public function hideActions($hideActions = false)
    {
        $this->hideActions = $hideActions;

        return $this;
    }

    public function resource(string $resource)
    {
        if (class_exists($resource)) {
            $resource = Move::resourceKey($resource);
        }

        $this->showResource = $resource;

        return $this;
    }
}
