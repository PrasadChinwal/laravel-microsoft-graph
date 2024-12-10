<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events\Attendees;

use Spatie\LaravelData\Data;

class EmailAddress extends Data
{
    public function __construct(
        public ?string $name,
        public ?string $address,
    ) {}
}