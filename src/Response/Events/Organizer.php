<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

use PrasadChinwal\MicrosoftGraph\Response\Events\Attendees\EmailAddress;
use Spatie\LaravelData\Data;

class Organizer extends Data
{
    public function __construct(
        public ?EmailAddress $emailAddress,
    ) {}
}