<?php

namespace PrasadChinwal\MicrosoftGraph\Response\Events\Recurrence;

use Spatie\LaravelData\Data;

class Pattern extends Data
{
    public function __construct(
        public ?int $dayOfMonth,
        public ?array $dayOfWeek,
        public ?string $firstDayOfWeek,
        public ?string $index,
        public ?int $interval,
        public ?int $month,
        public ?string $type,
    ) {}
}