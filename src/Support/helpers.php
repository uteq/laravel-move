<?php

if (! function_exists('resolve_resource')) {
    function resolve_resource($resource)
    {
        $resource = str_replace('/', '.', $resource);

        if (! app()->has('resource.' . $resource)) {
            throw new Exception(sprintf(
                '%s: The requested resource %s does not exist or has not been added',
                __METHOD__,
                str_replace('.', '/', $resource),
            ));
        }

        $resource = app()->get('resource.' . $resource);

        return $resource;
    }
}
