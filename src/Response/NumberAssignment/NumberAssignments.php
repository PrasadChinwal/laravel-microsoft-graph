<?php

namespace PrasadChinwal\MicrosoftGraph\Response\NumberAssignment;

use Spatie\LaravelData\Data;

class NumberAssignments extends Data
{
    public function __construct(
        public ?string $id,
        public ?string $telephoneNumber,
        public ?string $operatorId,
        public ?string $numberType,
        public ?string $activationState,
        public ?array $capabilities,
        public ?string $locationId,
        public ?string $civicAddressId,
        public ?string $networkSiteId,
        public ?string $assignmentTargetId,
        public ?string $assignmentCategory,
        public ?string $portInStatus,
        public ?string $assignmentStatus,
        public ?string $isoCountryCode,
        public ?string $city,
        public ?string $numberSource,
        public ?array $supportedCustomerActions,
        public ?array $reverseNumberLookupOptions,
    )
    {}
}