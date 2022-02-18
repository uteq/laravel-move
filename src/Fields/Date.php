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
        $this->valueCallback = function ($date) {
            if (is_string($date)) {
                return $date;
            }

            return optional($date)->format('d-m-Y');
        };

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

    public function altFormat(string $format)
    {
        $this->dateConfig['altFormat'] = $format;

        return $this;
    }

    public function enableTime($enableTime = true)
    {
        $this->dateConfig['enableTime'] = $enableTime;

        return $this;
    }

    public function noCalendar($noCalendar = true)
    {
        $this->dateConfig['noCalendar'] = $noCalendar;

        return $this;
    }

    public function resolveUsing($valueCallback)
    {
        $this->valueCallback = $valueCallback;

        return $this;
    }

    public function asTime($placeholder = null)
    {
        return $this->config([
                'dateFormat' => 'H:i',
                'altFormat' => 'H:i',
            ])
            ->placeholder($placeholder ?: __('Choose a time'))
            ->noCalendar()
            ->enableTime();
    }

    public function config(array $config)
    {
        $this->dateConfig = array_replace_recursive($this->dateConfig, $config);

        return $this;
    }
}
