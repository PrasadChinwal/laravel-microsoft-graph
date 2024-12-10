<?php

namespace PrasadChinwal\MicrosoftGraph\Response\User;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class UserCollection extends Data
{
    public function __construct(
        /** @var Collection<int, User> */
        public ?Collection $value,
    ) {}
}
