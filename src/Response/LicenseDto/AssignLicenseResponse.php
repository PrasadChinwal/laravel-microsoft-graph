<?php

namespace PrasadChinwal\MicrosoftGraph\Response\LicenseDto;

use Spatie\LaravelData\Data;

class AssignLicenseResponse extends Data
{
    public function __construct(
        public bool $accountEnabled,
        /* @var NewLicenseCollection<int, NewLicense> */
        public NewLicenseCollection $assignedLicenses,
        public ?string $city,
        public ?string $companyName,
    ) {}
}
