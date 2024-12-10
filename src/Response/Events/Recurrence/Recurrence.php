<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events\Recurrence;

use Spatie\LaravelData\Data;

class Recurrence extends Data
{
    public function __construct(
        public ?Pattern $pattern,
        public ?Range $range,
    ) {}
}