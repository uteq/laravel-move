<?php

namespace Uteq\Move\Fields;

use Closure;
use Uteq\Move\Actions\LivewireCloseModal;
use Uteq\Move\Facades\Move;
use Uteq\Move\Fields\Concerns\WithStackableFields;

class Table extends Panel
{
    public string $component = 'form.table';

    public string $tableResource;

    public array $showFields = [];

    public array $hideFields = [];

    protected Closure $disableDeleteFor;

    public function __construct(
        $name,
        public string $resourceClass,
        array $fields = null
    )
    {
        $this->id = static::class . md5($resourceClass);

        $this->withMeta([
            'limit' => 10,
            'with_delete' => false,
            'with_grid' => false,
            'full_colspan' => true,
            'with_add_button' => false,
            'disable_delete_for' => null,
            'display' => 'normal',
        ]);

        $fields ??= [
            Placeholder::make(),
        ];

        $this->resource($resourceClass);

        parent::__construct($name, $fields);
    }

    public function can($actions): static
    {
        foreach ($actions as $action) {
            if ($action === 'delete') {
                $this->withDelete();
            }

            if ($action === 'add') {
                $this->withAddButton();
            }

            if ($action === 'edit') {
                $this->withEditButton();
            }
        }

        return $this;
    }

    public function showFields(array $showFields)
    {
        $this->showFields = $showFields;

        return $this;
    }

    public function hideFields(array $hideFields)
    {
        $this->hideFields = $hideFields;

        return $this;
    }

    public function fields()
    {
        $fields = ($this->fields ?? null) ?: (new ($this->resourceClass)($this->model))->fields();

        return collect($fields)
            ->filter(fn ($field) => in_array($field->attribute, $this->showFields, true))
            ->filter(fn ($field) => !in_array($field->attribute, $this->hideFields, true))
            ->each(function ($field) {
                $field->storePrefix = $field->defaultStorePrefix . '.' . $this->id();
                $field->hideName();
                $field->generateStoreAttribute();
                $field->withMeta([
                    'with_grid' => false,
                ]);
            });
    }

    public function resource(string $resource)
    {
        if (class_exists($resource)) {
            $resource = Move::resourceKey($resource);
        }

        $this->tableResource = $resource;

        return $this;
    }

    public function withAddButton($withAddButton = true)
    {
        $this->meta['with_add_button'] = $withAddButton;

        return $this;
    }

    public function withDelete($withDelete = true)
    {
        $this->meta['with_delete'] = $withDelete;

        return $this;
    }

    public function withEditButton($withEdit = true)
    {
        $this->meta['with_edit'] = $withEdit;

        return $this;
    }

    public function disableDeleteFor(Closure $closure): self
    {
        $this->disableDeleteFor = $closure;

        return $this;
    }

    public function getDisableDeleteFor()
    {
        return $this->disableDeleteFor ?? null;
    }

    public function closeModalAfterAction(): static
    {
        return $this->redirects([
            'create' => LivewireCloseModal::asClosure(),
            'update' => LivewireCloseModal::asClosure(),
            'delete' => LivewireCloseModal::asClosure(),
        ]);
    }
}
