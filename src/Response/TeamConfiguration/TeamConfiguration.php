<?php

namespace PrasadChinwal\MicrosoftGraph\Response\TeamConfiguration;

use Spatie\LaravelData\Data;

class TeamConfiguration extends Data
{
    public function __construct(
        public ?bool $isEnterpriseVoiceEnabled,
        public ?array $featureTypes,
        public ?string $id,
        public ?string $tenantId,
        public ?string $userPrincipalName,
        public ?string $modifiedDateTime,
        public ?string $accountType,
        /** @var TelephoneNumberCollection<int, TelephoneNumber> */
        public TelephoneNumberCollection $telephoneNumbers,
        public ?array $effectivePolicyAssignments,
    ) {}

    public function hasEnterpriseVoiceEnabled(): bool
    {
        return $this->isEnterpriseVoiceEnabled ?? false;
    }
}