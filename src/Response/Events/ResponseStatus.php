<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

use Spatie\LaravelData\Data;

class ResponseStatus extends Data
{
    public function __construct(
        public ?string $response,
        public ?string $time,
    ) {}
}