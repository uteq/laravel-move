<?php

namespace Uteq\Move\Fields;

use Illuminate\Validation\Rules\RequiredIf;

class Files extends Field
{
    public string $component = 'files-field';

    public bool $isMultiple = true;

    public bool $showRotate = true;

    public function __construct(
        string $name,
        string $attribute,
        callable $valueCallback = null
    ) {
        parent::__construct($name, $attribute, $valueCallback);

        $this->hideFromIndex();

        $this->customRules([
            $attribute . '.*' => ['file','mimes:png,jpg,jpeg,pdf,heic','max:102400'],
            $attribute . '.0' => [
                new RequiredIf(fn () => $this->isRequired()),
            ],
        ]);
    }

    public function isSingular(bool $value = true)
    {
        $this->isMultiple = ! $value;

        return $this;
    }

    public function media()
    {
        $this->resource->refresh();

        return $this->resource->getMedia($this->attribute);
    }

    public function showRotate($showRotate = true)
    {
        $this->showRotate = true;

        return $this;
    }

    public function hideRotate($showRotate = false)
    {
        $this->showRotate = false;

        return $this;
    }
}
