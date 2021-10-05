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
        $this->afterValueCallback(fn ($date) => optional($date)->format('d-m-Y'));

        // Makes sure it also handles values like 19-08-1990T23:00:00.000000Z
        $this->beforeStore[] = fn ($date) => str_contains($date, 'T')
            ? date('d-m-Y', strtotime($date))
            : $date;
    }

    public function format(string $format)
    {
        $this->dateConfig['dateFormat'] = $format;

        return $this;
    }

    public function resolveUsing($valueCallback)
    {
        $this->valueCallback = $valueCallback;

        return $this;
    }

    public function config(array $config)
    {
        $this->dateConfig = array_replace_recursive($this->dateConfig, $config);

        return $this;
    }
}
