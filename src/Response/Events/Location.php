<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events;

class Location
{
    public function __construct(
        public ?string $displayName,
        public ?string $locationType,
        public ?string $uniqueId,
        public ?string $uniqueIdType,
    ) {}
}