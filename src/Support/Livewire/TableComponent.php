<?php

namespace Uteq\Move\Support\Livewire;

use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Uteq\Move\Requests\ResourceIndexRequest;
use Uteq\Move\Support\Livewire\Concerns\HasCrud;
use Uteq\Move\Support\Livewire\Concerns\HasFilter;

abstract class TableComponent extends Component
{
    use HasFilter;
    use HasCrud;

    public function getTotalProperty()
    {
        return $this->query()->count();
    }

    abstract public function query(): Builder;

    public function render(ResourceIndexRequest $request)
    {
        //
    }
}
