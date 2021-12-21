<?php

namespace Uteq\Move\Contracts;

interface ElementInterface
{
    public function addDependencies($dependencies): static;

    public function applyResourceData($model, $resourceForm = null, $resource = null): static;

    public function isShownOn($action, $resource = null, $request = null): bool;
}
