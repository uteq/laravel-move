<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Concerns\Makeable;

class Panel
{
    use Makeable;

    public ?string $name = null;
    public array $fields;

    public function __construct(?string $name = null, array $fields = [])
    {
        $this->name = $name;
        $this->fields = $fields;
    }

    public function resolveFields($resource)
    {
        collect($this->fields)
            ->each(fn (Field $field) => $field->resolveForDisplay($resource));

        return $this;
    }

    public function empty()
    {
        return ! count($this->fields);
    }
}
