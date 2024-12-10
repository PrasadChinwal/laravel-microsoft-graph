<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events\Attendees;

use Spatie\LaravelData\Data;

class Status extends Data
{
    public function __construct(
        public ?string $response,
        public ?string $time,
    ) {}
}