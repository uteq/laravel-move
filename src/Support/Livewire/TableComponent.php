<?php

namespace Uteq\Move\Support\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Uteq\Move\Requests\ResourceIndexRequest;
use Uteq\Move\Support\Livewire\Concerns\HasCrud;
use Uteq\Move\Support\Livewire\Concerns\HasFilter;

abstract class TableComponent extends Component
{
    use HasFilter, HasCrud;

    public function getTotalProperty()
    {
        return $this->query()->count();
    }

    abstract function query(): Builder;

    public function render(ResourceIndexRequest $request)
    {
        //
    }
}
