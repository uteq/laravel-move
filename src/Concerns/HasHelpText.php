<?php

namespace Uteq\Move\Concerns;

trait HasHelpText
{
    /**
     * The help text for the metric.
     *
     * @var string
     */
    public $helpText;

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

        return is_callable($helpText)
            ? $helpText()
            : $helpText;
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
}
