<?php

namespace Uteq\Move\Concerns;

trait WithModal
{
    public ?string $showModal = null;

    public function initializeWithModal(): void
    {
        $this->listeners = array_replace([
            'closeModal' => 'closeModal',
        ], $this->listeners);
    }

    public function closeModal(
        string $message = null,
        string $messageKey = 'message'
    ): void {
        $this->showModal = null;

        if ($message) {
            session()->flash($messageKey, $message);
        }
    }
}
