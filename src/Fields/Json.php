<?php

namespace Uteq\Move\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Uteq\Move\Resource;

class Json extends Field
{
    public array $blueprint = [];
    public string $component = 'json';
    public bool $editableKeys = true;
    public string $indexDisplayType = 'modal';
    public string $addItemText = '+ Add item';
    public string $formDisplayType = 'form';

    public $fields;

    public function fields(array $fields)
    {
        $this->fields = $fields;

        return $this;
    }

    public function panel($resourceForm, $panelKey)
    {
        $model = $resourceForm->model;

        $resource = $model['store'][$this->attribute] ?? $model[$this->attribute] ?? [];

        $panel = new Panel('', $this->fields);
        $panel->id = $this->attribute ?? $this->unique;
        $panel->component = 'form.json-panel';
        $panel->formDisplayType = $this->formDisplayType;
        $panel->resolveFields($model);
        $panel->withMeta([
            'is_first' => $panelKey === 0,
        ]);
        $panel->withoutCard();

        collect($panel->fields)
            ->each(function ($field) use ($panelKey) {
                $field->storePrefix = $field->defaultStorePrefix . '.' . $this->attribute . '.' . $panelKey;
                $field->hideName();
                $field->generateStoreAttribute();
                $field->withMeta([
                    'with_grid' => false,
                ]);
            });

        return $panel->render($resource);
    }

    public function addItemText($text): self
    {
        $this->addItemText = $text;

        return $this;
    }

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

    public function removeRow($component, $field, $id)
    {
        Arr::forget($component->store, $field->attribute .'.'. $id);

        unset($component->store[$field->attribute][$id]);

        $component->updatedStore(Arr::get($component->store, $field->attribute), $field->attribute);
    }

    public function blueprint(array $blueprint): self
    {
        $this->blueprint = $blueprint;

        return $this;
    }

    public function editableKeys(bool $editableKeys = true): self
    {
        $this->editableKeys = $editableKeys;

        return $this;
    }

    public function indexDisplayType($type): self
    {
        $this->indexDisplayType = $type;

        return $this;
    }

    public function formDisplayType($type): self
    {
        $this->formDisplayType = $type;

        return $this;
    }


}
