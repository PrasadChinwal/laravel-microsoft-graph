<?php

namespace PrasadChinwal\MicrosoftGraph\Exceptions;

class AuthenticationException extends MicrosoftGraphException
{
    /**
     * Create a new authentication exception.
     */
    public static function tokenFetchFailed(string $reason = ''): self
    {
        $message = 'Failed to fetch access token from Microsoft Graph API.';

        if ($reason) {
            $message .= " Reason: {$reason}";
        }

        return new static($message);
    }

    /**
     * Create an exception for expired tokens.
     */
    public static function tokenExpired(): self
    {
        return new static('The access token has expired. Please re-authenticate.');
    }
}
