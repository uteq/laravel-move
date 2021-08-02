<?php

namespace Uteq\Move\Fields;

use Closure;
use Uteq\Move\Facades\Move;

class Table extends Panel
{
    public string $component = 'form.table';

    public string $tableResource;

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
            'with_grid' => false,
            'full_colspan' => true,
            'with_add_button' => false,
            'disable_delete_for' => null,
        ]);

        $fields ??= [
            Placeholder::make(uniqid()),
        ];

        $this->resource($resourceClass);

        parent::__construct($name, $fields);
    }

    public function fields()
    {
        $fields = ($this->fields ?? null) ?: (new ($this->resourceClass)($this->model))->fields();

        return collect($fields)
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

    public function disableDeleteFor(Closure $closure): self
    {
        $this->disableDeleteFor = $closure;

        return $this;
    }

    public function getDisableDeleteFor()
    {
        return $this->disableDeleteFor ?? null;
    }
}
