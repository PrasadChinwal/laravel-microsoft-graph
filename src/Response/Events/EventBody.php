<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

use Spatie\LaravelData\Data;

class EventBody extends Data
{
    public function __construct(
        public ?string $contentType,
        public ?string $content,
    ) {}
}