<?php

namespace Uteq\Move\Fields;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\RequiredIf;
use Uteq\Move\Contracts\FilesContract;

class Files extends Field implements FilesContract
{
    public string $component = 'files-field';

    public bool $isMultiple = true;

    public bool $showRotate = true;

    public array $accept = [
        'image/*',
        '.pdf',
        'application/pdf',
        'application/heic',
    ];

    public function __construct(
        string $name,
        string $attribute,
        callable $valueCallback = null
    ) {
        parent::__construct($name, $attribute, $valueCallback);

        $this->hideFromIndex();

        // TODO fix custom rules
        $this->customRules([
            $attribute . '.*' => ['file','mimes:png,jpg,jpeg,pdf,heic','max:102400'],
//            $attribute . '.0' => [
//                new RequiredIf(fn () => $this->isRequired()),
//            ],
        ]);
    }

    public function getAccept()
    {
        return implode(', ', $this->accept);
    }

    public function accept(array $accept)
    {
        $this->accept = $accept;

        return $this;
    }

    public function isSingular(bool $value = true)
    {
        $this->isMultiple = ! $value;

        return $this;
    }

    public function media()
    {
        $this->resource->refresh();

        $model = str_contains($this->attribute, '.')
            ? data_get($this->resource, Str::beforeLast($this->attribute, '.'))
            : $this->resource;

        // TODO: Find out why, in some cases, $model is an array
        if(is_array($model)){
            return null;
        }
        return $model?->getMedia(Str::afterLast($this->attribute, '.'));
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
