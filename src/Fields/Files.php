<?php

namespace Uteq\Move\Fields;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\RequiredIf;

class Files extends Field
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
        Closure $valueCallback = null
    ) {
        parent::__construct($name, $attribute, $valueCallback);

        $this->hideFromIndex();

//        $this->customRules([
//            $attribute . '.*' => ['file','mimes:png,jpg,jpeg,pdf,heic','max:102400'],
//            $attribute . '.0' => [
//                new RequiredIf(fn () => $this->isRequired()),
//            ],
//        ]);
    }

    public function getAccept(): string
    {
        return implode(', ', $this->accept);
    }

    public function accept(array $accept): static
    {
        $this->accept = $accept;

        return $this;
    }

    public function isSingular(bool $value = true): static
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

        return $model?->getMedia(Str::afterLast($this->attribute, '.'));
    }

    public function showRotate($showRotate = true): static
    {
        $this->showRotate = $showRotate;

        return $this;
    }

    public function hideRotate($showRotate = false): static
    {
        $this->showRotate($showRotate);

        return $this;
    }
}
