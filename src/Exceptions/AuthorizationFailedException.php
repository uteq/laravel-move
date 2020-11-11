<?php

namespace Uteq\Move\Exceptions;

use Throwable;

class AuthorizationFailedException extends \Exception
{
    public function __construct($message = null, $code = null, Throwable $previous = null)
    {
        parent::__construct($message ?? 'Unauthorized action attempted.', 0, $previous);

        $this->code = $code ?: 0;
    }
}
