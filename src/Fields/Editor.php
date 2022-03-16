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
        ['clean']
    ];

    public function init()
    {
        $this->beforeStore(fn ($value) => $this->fixUTF8($value));
    }

    public function themeBubble()
    {
        return $this->theme('bubble');
    }

    public function themeSnow()
    {
        return $this->theme('snow');
    }

    public function theme(string $theme)
    {
        $this->theme = $theme;

        return $this;
    }

    public function toolbar($toolbar)
    {
        $this->toolbar = $toolbar;

        return $this;
    }

    public function fixUTF8($string): string
    {
        return $string;
    }
}
