<?php

namespace App\Exceptions;

use Illuminate\Contracts\Validation\ValidationException;
use Illuminate\Support\MessageBag;

class CustomValidationException extends ValidationException
{
    protected $messages;

    /**
     * Create a new validation exception instance.
     *
     * @param array $messages
     */
    public function __construct(array $messages)
    {
        $this->messages = new MessageBag($messages);
    }

    public function errors()
    {
        return $this->messages;
    }

}