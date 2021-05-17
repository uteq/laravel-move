<?php

namespace Uteq\Move\Concerns;

use Closure;
use Illuminate\View\View;
use Livewire\CreateBladeView;

trait HasHelpText
{
    /**
     * The help text for the metric.
     *
     * @var string
     */
    public $helpText;

    public $helpTextAttributes = [];

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
    public function help($text)
    {
        $this->helpText = $text;

        return $this;
    }

    /**
     * Return the help text for the metric.
     *
     * @return string
     */
    public function getHelpText()
    {
        $helpText = $this->helpText;

        $data = array_replace_recursive($this->helpTextAttributes, [
            'store' => $this->resource['store'],
            'field' => $this
        ]);

        $view = is_callable($helpText)
            ? app()->call($helpText, $data)
            : $helpText;

        if (is_string($view)) {
            $view = app('view')->make(CreateBladeView::fromString($view));
        } else {
            return null;
        }

        throw_unless($view instanceof View,
            new \Exception('"view" method on ['.get_class($this).'] must return instance of ['.View::class.']'));

        return $view->with($data);
    }

    /**
     * Set the width for the help text tooltip.
     *
     * @return $this
     */
    public function helpWidth($helpWidth)
    {
        $this->helpWidth = $helpWidth;

        return $this;
    }

    /**
     * Return the width of the help text tooltip.
     *
     * @return string
     */
    public function getHelpWidth()
    {
        return $this->helpWidth;
    }

    public function alertWhen($color, $text, Closure $condition, $attributes = [])
    {
        $this->helpText = fn ($store) => $condition($store)
            ? <<<'blade'
                <x-move-alert color="{{ $color }}">{{ $text }}</x-move-alert>
              blade
            : null;

        $this->helpTextAttributes = array_replace_recursive([
            'text' => $text,
            'color' => $color,
        ], $attributes);

        return $this;
    }
}
