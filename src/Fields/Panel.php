<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Concerns\HasDependencies;
use Uteq\Move\Concerns\Makeable;

class Panel
{
    use Makeable;
    use HasDependencies;

    public ?string $name = null;
    public array $fields;
    public string $nameOnCreate;
    public string $nameOnUpdate;

    public function __construct(?string $name = null, array $fields = [])
    {
        $this->name = $name;
        $this->fields = $fields;

        if ($name) {
            $this->nameOnCreate = $name;
            $this->nameOnUpdate = $name;
        }
    }

    public function nameOnCreate(string $nameOnCreate)
    {
        $this->nameOnCreate = $nameOnCreate;

        return $this;
    }

    public function nameOnUpdate(string $nameOnUpdate)
    {
        $this->nameOnUpdate = $nameOnUpdate;

        return $this;
    }

    public function resolveFields($resource)
    {
        if ($this->name) {
            $this->name = isset($resource['id']) ? $this->nameOnUpdate : $this->nameOnCreate;
        }

        collect($this->fields)
            ->each(function (Field $field) use ($resource) {
                $field->addDependencies($this->dependencies)
                    ->resolveForDisplay($resource);
            });

        return $this;
    }

    public function empty()
    {
        return ! count($this->fields);
    }
}
