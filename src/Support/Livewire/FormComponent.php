<?php

namespace Uteq\Move\Support\Livewire;

use Livewire\Component;
use Uteq\Move\Support\Livewire\Concerns\StoresPreviousUrl;

abstract class FormComponent extends Component
{
    use StoresPreviousUrl;

    protected $baseRoute;
    protected $layout = 'layouts.app';
    protected $label;

    public function render()
    {
        return view($this->baseRoute . '.form')
            ->layout($this->layout, [
                'header' => $this->header(),
            ]);
    }

    public function redirects(): array
    {
        return [
            'create' => 'index',
            'update' => $this->previous ?: 'index',
            'cancel' => $this->previous ?: 'index',
        ];
    }

    public function header()
    {
        if (method_exists($this, 'label')) {
            $this->label = $this->label();
        }

        if (! $this->label) {
            throw new \Exception(sprintf(
                '%s: property `protected $label` should be defined',
                __METHOD__,
            ));
        }

        return $this->trader->id
            ? $this->label .' aanpassen'
            : $this->label .' toevoegen';
    }

    abstract public function rules(): array;
}
