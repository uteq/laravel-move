<?php

namespace Uteq\Move\Fields;

use Illuminate\Validation\Rules\RequiredIf;

class Files extends Field
{
    public string $component = 'files-field';

    public function __construct(
        string $name,
        string $attribute,
        callable $callableValue = null
    ) {
        parent::__construct($name, $attribute, $callableValue);

        $this->hideFromIndex();

        $this->customRules([
            $attribute . '.*' => ['file','mimes:png,jpg,jpeg,pdf,heic','max:102400'],
            $attribute . '.0' => [
                new RequiredIf(fn () => $this->isRequired()),
            ],
        ]);
    }

    public function media()
    {
        $this->resource->refresh();

        return $this->resource->getMedia($this->attribute);
    }
}
