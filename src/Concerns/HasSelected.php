<?php

namespace Uteq\Move\Concerns;

trait HasSelected
{
    public $has_selected = false;
    public $select_type = [];
    public $selected = [];
    public $meta = [];

    public function initializeHaSelected(): void
    {
        $this->computeHasSelected();
    }

    public function countSelected()
    {
        return collect($this->selected)
            ->filter(fn ($value) => $value !== false)
            ->count();
    }

    public function hasSelected(): bool
    {
        return $this->countSelected() > 0;
    }

    public function computeHasSelected(): void
    {
        $this->has_selected = $this->hasSelected();
    }

    public function updatedSelectType($value, $key): void
    {
        if ($key === 'table' && $value === false) {
            $this->select_type['all'] = false;
        }

        if ($key === 'all' && $value === true) {
            $this->select_type['table'] = true;
        }

        $this->setSelected();
    }

    public function setSelected(): void
    {
        $this->selected = [];

        if (collect($this->select_type)->filter(fn ($value) => $value)->count()) {
            $collection = ! $this->hasSelectType('all')
                ? $this->collection()
                : $this->query()->get();

            foreach ($collection as $item) {
                $this->selected[$item->getKey()] = $item->getKey();
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

    public function updatedSelected($value, $key): void
    {
        $this->meta['selected'] = true;

        $this->computeHasSelected();
    }

    public function selectedCollection()
    {
        return ! $this->hasSelectType('all')
            ? $this->collection()->filter(fn ($item) => $this->selected($item->getKey()))
            : $this->query()->get();
    }

    public function selected($key, $default = false): bool
    {
        return (bool)($this->selected[$key] ?? $default);
    }
}
