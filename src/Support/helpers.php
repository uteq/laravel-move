<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;
use Symfony\Component\Debug\Exception\FatalThrowableError;

/**
 * @return false|string
 */
function render($string, $__data = []): string|false
{
    $__data['__env'] = app(\Illuminate\View\Factory::class);
    $__php = Blade::compileString($string);

    $obLevel = ob_get_level();
    ob_start();
    extract($__data, EXTR_SKIP);

    try {
        eval('?' . '>' . $__php);
    } catch (Exception $e) {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw $e;
    } catch (Throwable $e) {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        throw new FatalThrowableError($e);
    }

    return ob_get_clean();
}

function move(): \Uteq\Move\Facades\Move
{
    return new \Uteq\Move\Facades\Move();
}

function move_class_to_label($class): string
{
    return (string) Str::of(class_basename($class))
        ->snake(',')
        ->title()
        ->plural();
}

/**
 * Expands a dot notation to a multidimensional array
 *
 * ```['person.name' => 'jack']```
 *
 * Will become
 *
 * ```[
 *     'person' => [
 *         'name' => 'jack'
 *     ]
 * ]```
 *
 * @param array $array
 * @return array
 */
function move_arr_expand(array $array) {
    $valueSet = [];
    foreach ($array as $key => $item) {
        Arr::set($valueSet, $key, $item);
    }
    return $valueSet;
}
