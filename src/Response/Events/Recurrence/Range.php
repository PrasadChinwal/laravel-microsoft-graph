<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events\Recurrence;

use Spatie\LaravelData\Data;

class Range extends Data
{
    public function __construct(
        public ?string $endDate,
        public ?int $numberOfOccurrences,
        public ?string $recurrenceTimeZone,
        public ?string $startDate,
        public ?string $type,
    ) {}
}