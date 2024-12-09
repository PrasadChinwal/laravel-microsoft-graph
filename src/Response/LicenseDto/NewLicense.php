<?php

namespace PrasadChinwal\MicrosoftGraph\Response\LicenseDto;

use Spatie\LaravelData\Data;

class NewLicense extends Data
{
    public function __construct(
        public array $disabledPlans,
        public string $skuId,
    ) {}
}
