<?php

namespace Uteq\Move\Fields;

class Date extends Field
{
    public string $component = 'date-field';

    public array $dateConfig = [
        "dateFormat" => "d-m-Y",
        "altFormat" => "d-m-Y",
        "altInput" => true,
        "allowInput" => true,
    ];

    public function init()
    {
        $this->callableValue = fn ($date) => optional($date)->format('d-m-Y');
    }

    public function format(string $format)
    {
        $this->dateConfig['dateFormat'] = $format;

        return $this;
    }

    public function resolveUsing($callableValue)
    {
        $this->callableValue = $callableValue;

        return $this;
    }

    public function config(array $config)
    {
        $this->dateConfig = array_replace_recursive($this->dateConfig, $config);

        return $this;
    }
}
