<?php

namespace PrasadChinwal\MicrosoftGraph\Calendar\Event\Eventable;

class Attendee
{
    /**
     * Invitee email address.
     */
    public string $email;

    /**
     * Invitee Name
     */
    public ?string $name;

    /**
     * Invitee presence.
     */
    public bool $required = false;

    /**
     * Create a new invitee instance
     */
    public function __construct(string $email, string $name = null, bool $required = false)
    {
        $this->email = $email;
        $this->name = $name;
        $this->required = $required;
    }
}
