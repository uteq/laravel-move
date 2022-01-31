<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Concerns\WithVersion;
use Uteq\Move\Support\Encoding;

class Editor extends Textarea
{
    use WithVersion;

    protected $version = 1;

    public string $component = 'editor-field';

    public string $theme = 'snow';

    public array $toolbar = [
        [[ 'header' => [1, 2, 3, 4, 5, 6, false] ]],
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [[ 'list' => 'ordered'], [ 'list' => 'bullet' ]],
        [[ 'align' => [] ]],
        ['clean'],
    ];

    public array $settings = [];

    public function init(): void
    {
        $this->beforeStore(fn ($value) => $this->fixUTF8($value));
    }

    public function fixUTF8($string): ?string
    {
        if (empty($string)) {
            return null;
        }

        if (mb_detect_encoding(mb_convert_encoding($string, 'Windows-1251', 'UTF-8'), 'Windows-1251,UTF-8', true) === 'UTF-8') {
            return utf8_decode($string) ?: $string;
        }

        try {
            $string = Encoding::fixUTF8($string, Encoding::ICONV_TRANSLIT);
        } catch (\ErrorException $e) {
            // Assuming TRANSLIT threw an error because of iconv
            //  https://stackoverflow.com/questions/8727735/iconv-detected-an-illegal-character-in-input-string
            //  we now return to simple decode and using iconv ignore.
            $string = Encoding::fixUTF8(utf8_decode($string), Encoding::ICONV_IGNORE);
        }

        return $string;
    }


    public function settings(array $settings): static
    {
        $this->settings = array_replace_recursive($this->settings, $settings);

        return $this;
    }

    public function toolbar(array $toolbar): static
    {
        $this->toolbar = $toolbar;

        return $this;
    }
}
