<?php

namespace PrasadChinwal\MicrosoftGraph\Builder\License;

class NewLicense
{
    public array $disabledPlans = [];

    public string $skuId;

    /**
     * Construct a new AddLicense instance.
     *
     * @param  array  $disabledPlans  List of disabled plans.
     * @param  string  $skuId  The SKU ID of the license.
     */
    public function __construct(array $disabledPlans, string $skuId)
    {
        $this->disabledPlans = $disabledPlans;
        $this->skuId = $skuId;
    }

    public static function make(array $disabledPlans, string $skuId): NewLicense
    {
        return new self($disabledPlans, $skuId);
    }
}
