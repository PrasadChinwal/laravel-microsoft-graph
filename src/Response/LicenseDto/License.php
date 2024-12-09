<?php

namespace PrasadChinwal\MicrosoftGraph\Response\LicenseDto;

use Spatie\LaravelData\Data;

class License extends Data
{
    public function __construct(
        public string $id,
        public string $skuId,
        public string $skuPartNumber,
        /** @var ServicePlanCollection<int, ServicePlan> */
        public ServicePlanCollection $servicePlans
    ) {}
}
