<?php

namespace Uteq\Move\Fields;

use Illuminate\Support\Str;

class Form extends Panel
{
    public string $component = 'form.form';

    public string $flow = 'row';

    public ?string $helpText = null;

    public bool $flex = false;

    public function init()
    {
        $this->unique = md5($this->name);

        $this->withMeta([
            'help_text_location' => 'hidden',
        ]);

        /** @var \Support\Fields\Field $field */
        foreach ($this->fields as &$field) {
            if (! $field instanceof Field) {
                continue;
            }

            $field->stacked();
            $field->flex = false;
            $field->withMeta([
                'stacked_classes' => 'bg-white w-full last:border-b-0 border-gray-100',
            ]);
        }
    }

    public function render($model, array $data = [])
    {
        $component = $data['component'] ?? $this->component;
        $folder = $data['folder'] ?? $this->folder;

        return view($folder . $component, array_replace_recursive([
            'panel' => $this,
            'model' => $model,
        ], $data));
    }

    public function getName()
    {
        return $this->name;
    }

    public function helpText($helpText)
    {
        $this->helpText = $helpText;

        return $this;
    }

    public function getHelpText()
    {
        return $this->helpText;
    }

    public function isRequired()
    {
        return false;
    }
}
