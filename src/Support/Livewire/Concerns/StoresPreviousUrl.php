<?php

namespace Uteq\Move\Support\Livewire\Concerns;

use Illuminate\Support\Facades\Session;

trait StoresPreviousUrl
{
    public $previous;
    public $storesPrevious = true;

    public function initializeStoresPreviousUrl(): void
    {
        if (! $this->storesPrevious) {
            return;
        }

        if (request()->method() !== 'POST') {
            $this->previous = url()->previous();

            Session::put(static::class . '.previous', $this->previous);
        } else {
            $this->previous = Session::get(static::class . '.previous');
        }
    }
}
