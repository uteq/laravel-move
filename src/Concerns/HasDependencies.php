<?php

namespace Uteq\Move\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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

    public function dependsOnCall(string $field, Closure $callback)
    {
        $this->dependencies[$field] = ['call' => $callback];

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
        /** @psalm-suppress UnusedClosureParam */
        $rules = [
            'callback' => fn ($value, $result) => $value($result, $this, $data),
            'call' => fn ($value, $result) => app()->call($value, ['result' => $result, 'field' => $this, 'store' => $data]),
            'value' => fn ($value, $result) => $result == $value,
            'not' => fn ($value, $result) => $result != $value,
            'not_null' => fn ($value, $result) => $result !== null,
            'empty' => fn ($value, $result) => empty($result),
            'nullOrZero' => fn ($value, $result) => in_array($result, [null, 0, '0']),
        ];

        if (($this->type ?? 'form') !== 'form') {
            return true;
        }

        $dataWithoutDots = collect($data)
            ->filter(fn ($value, $key) => ! Str::contains($key, '.'))
            ->toArray();

        return $this->areDependenciesSatisfiedWithData($rules, $data)
            || $this->areDependenciesSatisfiedWithData($rules, $dataWithoutDots);
    }

    private function areDependenciesSatisfiedWithData(array $rules, $data): bool
    {
        foreach ($this->dependencies as $field => $condition) {
            foreach ($condition as $type => $value) {
                if (! $rules[$type]($value, $data[$field] ?? $data->{$field} ?? Arr::get($data, $field, false))) {
                    return false;
                }
            }
        }

        return true;
    }
}
