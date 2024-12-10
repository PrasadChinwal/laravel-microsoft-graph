<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

use Spatie\LaravelData\Data;

class EventTime extends Data
{
    public function __construct(
        public ?string $dateTime,
        public ?string $timeZone,
    ) {}
}