<?php

namespace Uteq\Move\Fields;

use Illuminate\Database\Eloquent\Collection;
use Uteq\Move\Fields\Concerns\HasResource;

class HasMany extends Select
{
    use HasResource;

    public array $settings = [
        'multiple' => true,
    ];

    public string $relation;

    public function __construct(string $name, string $attribute = null, string $resource = null)
    {
        $this->resourceName = $this->findResourceName($name, $resource);

        parent::__construct($name, $attribute, function ($data, $model, $c) {
            return $data instanceof Collection
                ? $data->mapWithKeys(fn ($item) => [$item->id => $item->name])
                : $data;
        });
    }

    public function relation(string $relation)
    {
        $this->relation = $relation;

        return $this;
    }

    public function handleAfterStore($value, $field, $model, $data)
    {
        parent::handleAfterStore($value, $field, $model, $data);

        $relation = $this->relation;

        $ids = collect($value)
            ->filter(fn ($value) => ! empty($value))
            ->toArray();

        $model->{$relation}()->sync($ids);

        return $this;
    }
}
