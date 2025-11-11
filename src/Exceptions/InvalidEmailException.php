<?php

namespace PrasadChinwal\MicrosoftGraph\Exceptions;

class InvalidEmailException extends MicrosoftGraphException
{
    /**
     * Create a new invalid email exception.
     */
    public static function empty(): self
    {
        return new static('Email address cannot be empty.');
    }

    /**
     * Create an exception for invalid email format.
     */
    public static function invalidFormat(string $email): self
    {
        return new static("The email address '{$email}' is not valid.");
    }
}
