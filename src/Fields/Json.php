<?php

namespace Uteq\Move\Fields;

class Json extends Field
{
    public string $component = 'json';
    public bool $editableKeys = true;
    public array $blueprint = [];
    public string $indexDisplayType = 'modal';

    public function isPlaceholder(bool $value = true): static
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

    public function removeParagraph($component, $field, $id): void
    {
        unset($component->store[$field->attribute][$id]);
    }

    public function blueprint(array $blueprint): static
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    public function editableKeys(bool $editableKeys = true): static
    {
        $this->editableKeys = $editableKeys;

        return $this;
    }

    public function indexDisplayType($type): static
    {
        $this->indexDisplayType = $type;

        return $this;
    }
}
