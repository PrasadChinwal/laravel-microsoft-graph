<?php

namespace PrasadChinwal\MicrosoftGraph\Response\User;

use Spatie\LaravelData\Data;

class User extends Data
{
    public function __construct(
        public array $businessPhones,
        public ?string $displayName,
        public ?string $givenName,
        public ?string $jobTitle,
        public ?string $mail,
        public ?string $mobilePhone,
        public ?string $officeLocation,
        public ?string $preferredLanguage,
        public ?string $surname,
        public ?string $id,
    ) {}
}
