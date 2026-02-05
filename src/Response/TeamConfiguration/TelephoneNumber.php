<?php

namespace PrasadChinwal\MicrosoftGraph\Response\TeamConfiguration;

use Spatie\LaravelData\Data;

class TelephoneNumber extends Data
{
    public function __construct(
        public ?string $telephoneNumber,
        public ?string $assignmentCategory,
    )
    {}
}