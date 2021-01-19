<?php

namespace Uteq\Move\Actions;

use Illuminate\Support\Collection;
use Uteq\Move\Concerns\Makeable;
use Uteq\Move\Concerns\Metable;

class Action
{
    use Makeable;
    use Metable;

    /** The displayable name of the action. */
    public string $name;

    public string $component = 'confirm-action';

    /** The text to be used for the action's confirm button. */
    public string $confirmButtonText = 'Apply action';

    /** The text to be used for the action's cancel button. */
    public string $cancelButtonText = 'Cancel';

    /** The text to be used for the action's confirmation text. */
    public string $confirmText = 'Weet u zeker dat u deze actie uit wilt voeren?';

    public \Uteq\Move\Resource $resource;

    public Collection $collection;

    public function handleLivewireRequest(
        \Uteq\Move\Resource $resource,
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

    public function render(\Uteq\Move\Resource $resource)
    {
        return view('move::actions.' . $this->component, [
            'action' => $this,
            'resource' => $resource,
            'fields' => $this->resolveFields($resource),
        ]);
    }

    public function resolveFields(\Uteq\Move\Resource $resource)
    {
        $fields = [];
        foreach ($this->fields() as $field) {
            $fields[] = $field->type('update')
                ->applyResourceData($resource->resource)
                ->formAttribute('actionFields');
        }

        return $fields;
    }

    public function fields()
    {
        return [];
    }
}
