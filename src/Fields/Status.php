<?php

namespace Uteq\Move\Fields;

use Uteq\Move\Concerns\HasHelpText;

class Status extends Field
{
    use HasHelpText;

    public string $component = 'status-field';

    public string $infoPopupText;

    public function headerInfoPopup($text)
    {
        $this->infoPopupText = $text;

        return $this;
    }
}
