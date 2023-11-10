<?php

namespace Uteq\Move\Concerns;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Livewire\CreateBladeView;
use phpDocumentor\Reflection\Types\False_;

trait HasHelpText
{
    /**
     * The help text for the metric.
     *
     * @var string
     */
    protected $helpText;

    public $helpTextAttributes = [];

    public $hideHelpAtPositions = [];

    /**
     * The width of the help text tooltip.
     *
     * @var string
     */
    public $helpWidth = 250;

    /**
     * Add help text to the metric.
     *
     * @return $this
     */
    public function help($text, $position = 'below'): static
    {
        $this->helpText = $text;
        $this->meta['help_text_location'] = $position;

        return $this;
    }

    /**
     * Return the help text for the metric.
     *
     * @return string
     */
    public function getHelpText(): ?string
    {
        $helpText = $this->helpText;

        $undotedStore = [];
        foreach ($this->resource['store'] ?? [] as $key => $value) {
            Arr::set($undotedStore, $key, $value);
        }

        $data = array_replace_recursive($this->helpTextAttributes, [
            'store' => $undotedStore,
            'get' => fn ($key) => Arr::get($undotedStore, $key),
            'field' => $this
        ]);

        $view = is_callable($helpText)
            ? app()->call($helpText, $data)
            : $helpText;

        if (! is_string($view)) {
            return null;
        }

        $view = app('view')->make(CreateBladeView::fromString($view));

        throw_unless($view instanceof View,
            new \Exception('"view" method on ['.get_class($this).'] must return instance of ['.View::class.']'));

        return $view->with($data);
    }

    /**
     * Set the width for the help text tooltip.
     *
     * @return $this
     */
    public function helpWidth($helpWidth): static
    {
        $this->helpWidth = $helpWidth;

        return $this;
    }

    /**
     * Return the width of the help text tooltip.
     *
     * @return string
     */
    public function getHelpWidth(): string
    {
        return $this->helpWidth;
    }

    public function hideHelpAtPosition($position, $state = true): static
    {
        $this->hideHelpAtPositions[$position] = $state;

        return $this;
    }

    public function getHideHelpAtPosition($key)
    {
        return $this->hideHelpAtPositions[$key] ??= false;
    }

    public function alertWhen(Closure $condition, $color, $text, $attributes = []): static
    {
        $this->helpText = null;

        if (fn ($store) => $condition($store, $this)) {
            $this->helpText = ($attributes['hide_alert'] ?? false)
                ? $text
                : <<<'blade'
                    <x-move-alert color="{{ $color }}">{{ $text }}</x-move-alert>
                  blade;
        }

        $this->helpTextAttributes = array_replace_recursive([
            'text' => $text,
            'color' => $color,
        ], $attributes);

        return $this;
    }
}
