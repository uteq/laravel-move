<?php

namespace Uteq\Move\Fields;

use Closure;
use Uteq\Move\Actions\LivewireCloseModal;
use Uteq\Move\Facades\Move;

class Table extends Panel
{
    public string $component = 'form.table';

    public string $tableResource;

    public array $showFields = [];

    public array $hideFields = [];

    protected ?Closure $disableDeleteFor = null;

    public function __construct(
        $name,
        public string $resourceClass,
        array $fields = null
    ) {
        $this->id = static::class . md5($resourceClass);

        $this->withMeta([
            'limit' => 10,
            'with_delete' => false,
            'with_grid' => false,
            'full_colspan' => true,
            'disable_delete_for' => null,
            'display' => 'normal',

            'with_add_button' => false,
            'with_search' => true,
            'with_filters' => true,
            'with_checkbox' => true,
            'with_item_view' => true,
            'with_item_update' => true,
            'with_item_delete' => true,
        ]);

        $fields ??= [
            Placeholder::make(uniqid()),
        ];

        $this->resource($resourceClass);

        parent::__construct($name, $fields);
    }

    public function disableAll(): static
    {
        $this->meta['with_add_button'] = false;
        $this->meta['with_search'] = false;
        $this->meta['with_filters'] = false;
        $this->meta['with_checkbox'] = false;
        $this->meta['with_item_view'] = false;
        $this->meta['with_item_update'] = false;
        $this->meta['with_item_delete'] = false;

        return $this;
    }

    public function enableAll(): static
    {
        $this->meta['with_add_button'] = true;
        $this->meta['with_search'] = true;
        $this->meta['with_filters'] = true;
        $this->meta['with_checkbox'] = true;
        $this->meta['with_item_view'] = true;
        $this->meta['with_item_update'] = true;
        $this->meta['with_item_delete'] = true;

        return $this;
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

    public function showFields(array $showFields): static
    {
        $this->showFields = $showFields;

        return $this;
    }

    public function hideFields(array $hideFields): static
    {
        $this->hideFields = $hideFields;

        return $this;
    }

    public function fields(): \Illuminate\Support\Collection
    {
        $fields = ! empty($this->fields)
                ? $this->fields
                : (new ($this->resourceClass)($this->model))->fields();

        return collect($fields)
            ->filter(fn ($field) => in_array($field->attribute, $this->showFields, true))
            ->filter(fn ($field) => ! in_array($field->attribute, $this->hideFields, true))
            ->each(function ($field) {
                $field->storePrefix = $field->defaultStorePrefix . '.' . $this->id();
                $field->hideName();
                $field->generateStoreAttribute();
                $field->withMeta([
                    'with_grid' => false,
                ]);
            });
    }

    public function resource(string $resource): static
    {
        if (class_exists($resource)) {
            $resource = Move::resourceKey($resource);
        }

        $this->tableResource = $resource;

        return $this;
    }

    public function withAddButton($withAddButton = true): static
    {
        $this->meta['with_add_button'] = $withAddButton;

        return $this;
    }

    public function withDelete($withDelete = true): static
    {
        $this->meta['with_delete'] = $withDelete;

        return $this;
    }

    public function withEditButton($withEdit = true): static
    {
        $this->meta['with_edit'] = $withEdit;

        return $this;
    }

    public function disableDeleteFor(Closure $closure): static
    {
        $this->disableDeleteFor = $closure;

        return $this;
    }

    public function getDisableDeleteFor(): ?Closure
    {
        return $this->disableDeleteFor;
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
