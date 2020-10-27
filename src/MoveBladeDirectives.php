<?php

namespace Uteq\Move;

use Illuminate\Support\Str;

class MoveBladeDirectives
{
    public static function moveStyles($expression)
    {
        return '{!! \Uteq\Move\Facades\Move::styles('.$expression.') !!}';
    }

    public static function moveScripts($expression)
    {
        return '{!! \Uteq\Move\Facades\Move::scripts('.$expression.') !!}';
    }

    public static function livewire($expression)
    {
        $lastArg = trim(last(explode(',', $expression)));

        if (Str::startsWith($lastArg, 'key(') && Str::endsWith($lastArg, ')')) {
            $cachedKey = Str::replaceFirst('key(', '', Str::replaceLast(')', '', $lastArg));
            $args = explode(',', $expression);
            array_pop($args);
            $expression = implode(',', $args);
        } else {
            $cachedKey = "'".Str::random(7)."'";
        }

        return <<<EOT
<?php
if (! isset(\$_instance)) {
    \$html = \Livewire\Livewire::mount({$expression})->html();
} elseif (\$_instance->childHasBeenRendered($cachedKey)) {
    \$componentId = \$_instance->getRenderedChildComponentId($cachedKey);
    \$componentTag = \$_instance->getRenderedChildComponentTagName($cachedKey);
    \$html = \Livewire\Livewire::dummyMount(\$componentId, \$componentTag);
    \$_instance->preserveRenderedChild($cachedKey);
} else {
    \$response = \Livewire\Livewire::mount({$expression});
    \$html = \$response->html();
    \$_instance->logRenderedChild($cachedKey, \$response->id(), \Livewire\Livewire::getRootElementTagName(\$html));
}
echo \$html;
?>
EOT;
    }
}
