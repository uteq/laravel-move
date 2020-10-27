<?php

namespace Uteq\Move\Concerns;

trait HasSelected
{
    public $has_selected = false;
    public $select_type = [];
    public $selected = [];

    public function initializeHaSelected()
    {
        $this->computeHasSelected();
    }

    public function computeHasSelected()
    {
        $this->has_selected = collect($this->selected)
            ->filter(fn ($selected) => $selected === true)
            ->count();
    }

    public function updatedSelectType($value, $key)
    {
        if ($key === 'table' && $value === false) {
            $this->select_type['all'] = false;
        }

        if ($key === 'all' && $value === true) {
            $this->select_type['table'] = true;
        }

        $this->setSelected();
    }

    public function setSelected()
    {
        $this->selected = [];

        if (collect($this->select_type)->filter(fn ($value) => $value)->count()) {
            $collection = ! $this->hasSelectType('all')
                ? $this->collection()
                : $this->query()->get();

            foreach ($collection as $item) {
                $this->selected[$item->getKey()] = true;
            }
        }

        $this->computeHasSelected();
    }

    public function hasSelectType($type, $default = false)
    {
        return isset($this->select_type[$type])
            ? $this->select_type[$type]
            : $default;
    }

    public function updatedSelected($value, $key)
    {
        $this->computeHasSelected();
    }

    public function selectedCollection()
    {
        return ! $this->hasSelectType('all')
            ? $this->collection()->filter(fn ($item) => $this->selected($item->id))
            : $this->query()->get();
    }

    public function selected($key, $default = false)
    {
        return (bool)($this->selected[$key] ?? $default);
    }
}
