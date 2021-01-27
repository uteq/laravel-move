<?php

namespace Uteq\Move\Fields;

class Editor extends Textarea
{
    public string $component = 'editor-field';

    public function init()
    {
        $this->beforeStore(fn ($value) => $this->fixUTF8($value));
    }

    public function fixUTF8($string): string
    {
        if (mb_detect_encoding(mb_convert_encoding($string, 'Windows-1251', 'UTF-8'), 'Windows-1251,UTF-8', true) === 'UTF-8') {
            return utf8_decode($string);
        }

        return $string;
    }
}
