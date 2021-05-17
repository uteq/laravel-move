<?php

namespace Uteq\Move\Concerns;

use Uteq\Move\Resource;

trait HasParent
{
    public $parentResourceClass;
    public $parentModel;
    protected $parent;

    public function initHasParent()
    {
        if ($this->parentModel) {
            session()->put(static::class . '.' . $this->resource . '.parent', [
                'resource' => $this->parentResourceClass,
                'id' => $this->parentModel->id,
                'model' => get_class($this->parentModel),
            ]);
        }
    }

    public function setParent(Resource $parent)
    {
        session()->put(static::class . '.' . $this->resource . '.parent', [
            'resource' => get_class($parent),
            'id' => $parent->resource->id,
            'model' => get_class($parent->resource),
        ]);

        $this->parent = $parent;

        return $this;
    }

    public function parent()
    {
        if ($this->parent) {
            return $this->parent;
        }

        $parent = session(static::class . '.' . $this->resource . '.parent', [
            'resource' => $this->parentResourceClass,
            'id' => null,
            'model' => null,
        ]);

        if ($parent['resource']) {
            $model = $parent['id']
                ? $parent['model']::find($parent['id'])
                : new $parent['model']();

            $this->parent = new $parent['resource']($model);
        }

        return $this->parent;
    }

    public function parentRoute($baseRoute)
    {
        return str_replace($baseRoute . '/', '', $this->parent()->route());
    }
}
