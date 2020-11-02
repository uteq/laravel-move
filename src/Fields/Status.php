<?php

namespace Uteq\Move\Fields;

use Illuminate\Http\Request;
use Uteq\Move\Concerns\HasHelpText;

class Status extends Field
{
    use HasHelpText;

    public string $component = 'status-field';

    public string $infoPopupText;

    public $trueValue = true;

    public $falseValue = false;

    public function headerInfoPopup($text)
    {
        $this->infoPopupText = $text;

        return $this;
    }

    public function resolveAttribute($resource, $attribute)
    {
        return parent::resolveAttribute($resource, $attribute) == $this->trueValue
            ? true
            : false;
    }

    protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $model->{$attribute} = $value == true
                ? $this->trueValue
                : $this->falseValue;
        }
    }

    public function trueValue($value)
    {
        $this->trueValue = $value;

        return $this;
    }

    public function falseValue($value)
    {
        $this->falseValue = $value;

        return $this;
    }
}
