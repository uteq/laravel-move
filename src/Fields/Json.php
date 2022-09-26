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
    public string $addItemText;
    public string $formDisplayType = 'form';
    protected string $rowClasses;

    public $fields;
    public array $fieldMeta = [];
    public array $fieldOptions = [];

    public function init()
    {
        $this->addItemText = (string) __('+ Add item');

        $this->withMeta([
            'has_add_button' => true,
            'has_remove_button' => true,
        ]);

        return $this;
    }

    public function fields(array $fields, array $meta = [], array $options = [])
    {
        $this->fields = $fields;
        $this->fieldMeta = $meta ?: $this->fieldMeta;
        $this->fieldOptions = $options ?: $this->fieldOptions;

        return $this;
    }

    public function fieldMeta(array $meta = [])
    {
        $this->fieldMeta = $meta;

        return $this;
    }

    public function panel($resourceForm, $panelKey, $field)
    {
        $model = $resourceForm->model;

        $resource = $model['store'][$this->attribute] ?? $model[$this->attribute] ?? [];

        $panel = new Panel('', $this->fields);
        $panel->id = $this->attribute ?? $this->unique;
        $panel->component = 'form.json-panel';
        $panel->formDisplayType = $this->formDisplayType;
        $panel->resolveFields($model);
        $panel->withMeta(array_merge([
            'is_first' => $panelKey === 0,
        ], $this->customMeta(), $this->meta));
        $panel->withoutCard();
        $panel->removeRow = fn (...$args) => $this->removeRow(...$args);
        $panel->inline();

        collect($panel->fields)
            ->each(function ($field) use ($panelKey) {
                $field->storePrefix = $field->defaultStorePrefix . '.' . $this->attribute . '.' . $panelKey;
                $field->dirty = true;
                $field->hideName();
                $field->generateStoreAttribute();
                $field->withMeta(array_merge([
                    'with_grid' => false,
                ], $this->fieldMeta));

                foreach ($this->fieldOptions as $option => $values) {
                    $values ? $field->{$option}($values) : $field->{$option}();
                }
            });

        return $panel->render($resource, [
            'parentField' => $field,
            'panelKey' => $panelKey,
        ]);
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
        $index = count(Arr::get($component->store, $field->attribute, []) ?: []);

        $component->store = Arr::add($component->store, $field->attribute . '.' . $index, $this->blueprint);

        return $component;
    }

    public function removeRow($component, $field, $id)
    {
        Arr::forget($component->store, $field->attribute .'.'. $id);

        unset($component->store[$field->attribute][$id]);

        $component->updatedStore(
            Arr::get($component->store, $field->attribute),
            $field->attribute
        );
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

    public function rowClasses($rowClasses)
    {
        $this->rowClasses = $rowClasses;

        return $this;
    }

    public function customMeta(): array
    {
        $meta = [];

        if ($this->rowClasses ?? null) {
            $meta['stacked_classes'] = $this->rowClasses;
        }

        return $meta;
    }

    public function hideHeader($hideHeader = true)
    {
        $this->meta['hide_header'] = $hideHeader;

        return $this;
    }

    public function hideAddButton($hideAddButton = true): self
    {
        $this->meta['has_add_button'] = ! $hideAddButton;

        return $this;
    }

    public function hideRemoveButton($hideRemoveButton = true): self
    {
        $this->meta['has_remove_button'] = ! $hideRemoveButton;

        return $this;
    }

    public function values($store)
    {
        return Arr::get($store, $this->attribute, []) ?: [];
    }
}
