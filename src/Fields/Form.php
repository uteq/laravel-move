<?php

namespace Uteq\Move\Fields;

class Form extends Panel
{
    public string $component = 'form.form';

    public string $flow = 'row';

    public bool $flex = false;

    public function init()
    {
        $this->unique = md5($this->name);

        $this->withMeta([
            'help_text_location' => 'hidden',
        ]);

        $this->stackFields();
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

    public function isRequired()
    {
        return false;
    }
}
