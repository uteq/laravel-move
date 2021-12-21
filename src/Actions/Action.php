<?php

namespace Uteq\Move\Actions;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;
use Uteq\Move\Resource;

class Action
{
    use Makeable;
    use Metable;

    /** The displayable name of the action. */
    public string $name;

    public string $component = 'confirm-action';

    /** The text to be used for the action's confirm button. */
    public string $confirmButtonText;

    /** The text to be used for the action's cancel button. */
    public string $cancelButtonText;

    /** The text to be used for the action's confirmation text. */
    public string $confirmText;

    public Resource $resource;

    public Collection $collection;

    public function __construct()
    {
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        $this->confirmButtonText ??= __('Apply action');
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        $this->cancelButtonText ??= __('Cancel');
        /** @psalm-suppress RedundantPropertyInitializationCheck */
        $this->confirmText ??= __('Are you sure you want to perform this action?');
    }

    public function handleLivewireRequest(
        Resource $resource,
        Collection $collection,
        array $actionFields
    ) {
        $this->resource = $resource;
        $this->collection = $collection;

        if (method_exists($this, 'handle')) {
            /** @psalm-suppress InvalidArgument */
            return app()->call([$this, 'handle'], [
                'fields' => collect($actionFields),
                'models' => $collection,
            ]);
        }

        return null;
    }

    public function render(Resource $resource): View|Factory
    {
        return view('move::actions.' . $this->component, [
            'action' => $this,
            'resource' => $resource,
            'fields' => $this->resolveFields($resource),
        ]);
    }

    /**
     * @psalm-return list<mixed>
     */
    public function resolveFields(Resource $resource): array
    {
        $fields = [];
        foreach ($this->fields() as $field) {
            $fields[] = $field->type('update')
                ->applyResourceData($resource->resource)
                ->formAttribute('actionFields');
        }

        return $fields;
    }

    /**
     * @psalm-return array<empty, empty>
     */
    public function fields()
    {
        return [];
    }
}
