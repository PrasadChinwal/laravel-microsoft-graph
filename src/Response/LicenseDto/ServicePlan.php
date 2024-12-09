<?php

namespace PrasadChinwal\MicrosoftGraph\Response\LicenseDto;

use Spatie\LaravelData\Data;

class ServicePlan extends Data
{
    public function __construct(
        public string $servicePlanId,
        public string $servicePlanName,
        public string $provisioningStatus,
        public string $appliesTo,
    ) {}
}
