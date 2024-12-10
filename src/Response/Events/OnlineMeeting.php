<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

use Spatie\LaravelData\Data;

class OnlineMeeting extends Data
{
    public function __construct(
        public ?string $joinUrl,
    ) {}
}