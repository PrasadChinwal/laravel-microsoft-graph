<?php

namespace PrasadChinwal\MicrosoftGraph\Response\User;

use Spatie\LaravelData\Data;

class User extends Data
{
    public function __construct(
        public ?string $aboutMe,
        public ?bool $accountEnabled,
        public ?string $ageGroup,
        public ?string $birthday,
        public array $businessPhones,
        public ?string $city,
        public ?string $companyName,
        public ?string $country,
        public ?string $department,
        public ?string $displayName,
        public ?string $employeeId,
        public ?string $employeeType,
        public ?string $givenName,
        public ?string $employeeHireDate,
        public ?string $employeeLeaveDateTime,
        public ?string $interests,
        public ?string $jobTitle,
        public ?string $mail,
        public ?string $mailNickname,
        public ?string $mobilePhone,
        public ?string $mySite,
        public ?string $officeLocation,
        public ?array $otherMails,
        public ?array $pastProjects,
        public ?string $postalCode,
        public ?string $preferredLanguage,
        public ?string $preferredName,
        public ?array $responsibilities,
        public ?array $schools,
        public ?string $state,
        public ?string $streetAddress,
        public ?string $surname,
        public ?string $usageLocation,
        public ?string $userPrincipalName,
        public ?string $id,
    ) {}
}
