<?php

namespace Uteq\Move\Fields;

class Alert extends Panel
{
    public string $component = 'form.alert';

    public string $folder = 'move::';

    public string $color = 'primary';

    public $text = null;

    public $class = 'p-4 sm:p-6';

    public function __construct(string $type, $text)
    {
        $this->isPlaceholder();

        $this->color = $type;
        $this->text = $text;

        parent::__construct(uniqid(), [
            Placeholder::make(uniqid()),
        ]);
    }

    public function getText($store = [])
    {
        return is_callable($this->text)
            ? ($this->text)($store, $this)
            : $this->text;
    }

    public function text($text)
    {
        $this->text = $text;

        return $this;
    }

    public function class($class)
    {
        $this->class = $class;

        return $this;
    }
}
