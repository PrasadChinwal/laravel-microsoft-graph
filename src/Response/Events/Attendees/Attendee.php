<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events\Attendees;

use Spatie\LaravelData\Data;

class Attendee extends Data
{
    public function __construct(
        public ?string       $type,
        public ?Status       $status,
        public ?EmailAddress $emailAddress,
    ) {}
}