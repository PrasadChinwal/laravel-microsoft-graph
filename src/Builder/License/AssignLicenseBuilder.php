<?php

namespace PrasadChinwal\MicrosoftGraph\Builder\License;

class AssignLicenseBuilder
{
    public array $addLicenses;

    public array $removeLicenses = [];

    public function __construct(NewLicenseCollection $newLicenseCollection, array $removeLicenses = [])
    {
        $this->addLicenses = $newLicenseCollection->toArray();
        $this->removeLicenses = $removeLicenses;
    }

    /**
     * LicenseBuilder constructor.
     *
     * @param  array  $removeLicenses  An optional array of licenses to be removed.
     */
    public static function make(NewLicenseCollection $newLicenseCollection, array $removeLicenses = []): AssignLicenseBuilder
    {
        return new self($newLicenseCollection, $removeLicenses);
    }
}
