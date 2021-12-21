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

    public function headerInfoPopup($text): static
    {
        $this->infoPopupText = $text;

        return $this;
    }

    public function trueValue($value): static
    {
        $this->trueValue = $value;

        return $this;
    }

    public function falseValue($value): static
    {
        $this->falseValue = $value;

        return $this;
    }

    public function getResourceAttributeValue($resource, $attribute)
    {
        if (! $this->resolveTrueValue()) {
            return false;
        }

        return parent::getResourceAttributeValue($resource, $attribute) == $this->resolveTrueValue();
    }

    protected function fillAttributeFromRequest(Request $request, $requestAttribute, $model, $attribute): void
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
}
