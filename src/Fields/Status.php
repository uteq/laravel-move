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
        if (! $this->resolveTrueValue()) {
            return false;
        }

        return parent::resolveAttribute($resource, $attribute) == $this->resolveTrueValue()
            ? true
            : false;
    }

    protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $value = $request[$requestAttribute];

            $falseValue = $this->falseValue;

            $model->{$attribute} = $value == true
                ? $this->resolveTrueValue()
                : (is_callable($falseValue) ? $falseValue($this) : $falseValue);
        }
    }

    public function resolveTrueValue()
    {
        $trueValue = $this->trueValue;

        return (is_callable($trueValue) ? $trueValue($this, $this->attribute) : $trueValue);
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
