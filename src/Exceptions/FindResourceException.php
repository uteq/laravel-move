<?php

namespace Uteq\Move\Exceptions;

class FindResourceException extends \Exception
{
    public static function multipleImplementationsOfResource(string $resource, array $resources)
    {
        return new static(sprintf(
            'Multiple implementations detected of resource `%s` please namespace your resource to only let it return one resource. Detected implementations: `%s`',
            $resource,
            implode(',', $resources),
        ));
    }
}
