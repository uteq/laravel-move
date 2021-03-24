<?php

namespace Uteq\Move\Fields;

class Json extends Field
{
    public string $component = 'json';
    public bool $editableKeys = true;
    public array $blueprint = [];

    public function isPlaceholder(bool $value = true): self
    {
        $this->hide($value);

        $this->isPlaceholder = $value;

        return $this;
    }

    public function addRow($component, $field, $args = [])
    {
        $component->store[$field->attribute] ??= [];
        $component->store[$field->attribute][] = $this->blueprint;

        return $component;
    }

    public function blueprint(array $blueprint)
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    public function editableKeys(bool $editableKeys = true)
    {
        $this->editableKeys = $editableKeys;

        return $this;
    }
}
