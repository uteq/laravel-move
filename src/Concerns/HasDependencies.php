<?php

namespace Uteq\Move\Concerns;

trait HasDependencies
{
    protected $dependencies = [];

    /**
     * @param string $field
     * @param mixed|callable $value
     * @return $this
     */
    public function dependsOn(string $field, $value)
    {
        $type = is_callable($value) ? 'callback' : 'value';

        $this->dependencies[$field] = [$type => $value];

        return $this;
    }

    public function dependsOnNot(string $field, $value)
    {
        $this->dependencies[$field] = ['not' => $value];

        return $this;
    }

    public function dependsOnNotNull(string $field)
    {
        $this->dependencies[$field] = ['not_null' => true];

        return $this;
    }

    public function dependsOnEmpty(string $field)
    {
        $this->dependencies[$field] = ['empty' => true];

        return $this;
    }

    public function dependsOnNullOrZero(string $field)
    {
        $this->dependencies[$field] = ['nullOrZero' => true];

        return $this;
    }

    public function addDependencies($dependencies)
    {
        $this->dependencies = collect($this->dependencies)
            ->merge($dependencies)
            ->toArray();

        return $this;
    }

    public function areDependenciesSatisfied($data)
    {
        $rules = [
            'callback' => fn ($value, $result) => $value($result),
            'value' => fn ($value, $result) => $result == $value,
            'not' => fn ($value, $result) => $result != $value,
            'not_null' => fn ($value, $result) => $result !== null,
            'empty' => fn ($value, $result) => empty($result),
            'nullOrZero' => fn ($value, $result) => in_array($result, [null, 0, '0']),
        ];

        if (($this->type ?? 'form') !== 'form') {
            return true;
        }

        foreach ($this->dependencies as $field => $condition) {
            foreach ($condition as $type => $value) {

                if (! $rules[$type]($value, $data[$field] ?? $data->{$field} ?? null)) {
                    return false;
                }
            }
        }

        return true;
    }
}
