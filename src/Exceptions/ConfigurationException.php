<?php

namespace PrasadChinwal\MicrosoftGraph\Exceptions;

class ConfigurationException extends MicrosoftGraphException
{
    /**
     * Create an exception for missing configuration.
     */
    public static function missingCredentials(string $key): self
    {
        return new static(
            "Microsoft Graph configuration '{$key}' is not set. " .
            "Please ensure MICROSOFT_GRAPH_{$key} is set in your .env file."
        );
    }

    /**
     * Create an exception for invalid configuration.
     */
    public static function invalid(string $key, string $reason): self
    {
        return new static(
            "Microsoft Graph configuration '{$key}' is invalid: {$reason}"
        );
    }
}
